require('dotenv').config();
const { createBot, createProvider, createFlow, addKeyword, EVENTS } = require('@bot-whatsapp/bot');
const QRPortalWeb = require('@bot-whatsapp/portal');
const BaileysProvider = require('@bot-whatsapp/provider/baileys');
const MySQLAdapter = require('@bot-whatsapp/database/mysql');
const axios = require('axios');
const mysql = require('mysql2/promise');

const MYSQL_DB_HOST = process.env.MYSQL_DB_HOST;
const MYSQL_DB_USER = process.env.MYSQL_DB_USER;
const MYSQL_DB_PASSWORD = process.env.MYSQL_DB_PASSWORD;
const MYSQL_DB_NAME = process.env.MYSQL_DB_NAME;
const MYSQL_DB_PORT = process.env.MYSQL_DB_PORT;

const pool = require('./database/db');

async function obtenerDoctoresConHorarios() {
    const [rows] = await pool.execute(`
        SELECT d.name AS nombre, d.specialty AS especialidad, s.available_from AS desde, s.available_to AS hasta, s.days, s.price
        FROM doctors d
        JOIN doctor_schedules s ON s.doctor_id = d.id
        WHERE d.deleted_at IS NULL AND s.deleted_at IS NULL
    `);

    const agrupado = {};

    for (const row of rows) {
        if (!agrupado[row.nombre]) {
            agrupado[row.nombre] = {
                nombre: row.nombre,
                especialidad: row.especialidad,
                horarios: []
            };
        }
        agrupado[row.nombre].horarios.push({
            desde: row.desde,
            hasta: row.hasta,
            dias: row.days,
            precio: row.price
        });
    }

    return Object.values(agrupado);
}

async function obtenerHistorialPorNumero(numero) {

    const [rows] = await pool.execute(
        'SELECT keyword, answer, created_at FROM history WHERE phone = ? AND  answer!="__call_action__" and date(created_at)=date(now()) ORDER BY created_at DESC LIMIT 5',
        [numero]
    );
    return rows;
}
async function obtenerPreguntasFrecuentes() {
    const [rows] = await pool.execute(`
        SELECT pregunta, respuesta, precio
        FROM preguntas
        WHERE activo = 1 AND deleted_at IS NULL
    `);
    return rows;
}
async function consultarGemini(promptUsuario) {
    const apiKey = process.env.GEMINI_API_KEY;
    const url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' + apiKey;
    const body = {
        contents: [{ parts: [{ text: promptUsuario }] }]
    };

    try {
        const response = await axios.post(url, body);
        const text = response.data.candidates[0].content.parts[0].text;
        return text;
    } catch (error) {
        console.error('Error consultando Gemini:', error.message);
        return '⚠️ Lo siento, ocurrió un error procesando tu solicitud.';
    }
}
async function estaEnAtencionManual(phone) {
    const [rows] = await pool.execute('SELECT 1 FROM atencion_manual WHERE phone = ? AND deleted_at IS NULL LIMIT 1', [phone]);
    return rows.length > 0;
}


// Flujo general
const flowNaty = addKeyword([], { events: [EVENTS.MESSAGE] })
    .addAction(async (ctx, { flowDynamic }) => {
        const enAtencion = await estaEnAtencionManual(ctx.from);
        if (enAtencion) {
            console.log(`📵 El número ${ctx.from} está siendo atendido manualmente. No se responde.`);
            return; // No responder si está en atención
        }

        const horariosDoctores = await obtenerDoctoresConHorarios();
        const preguntasFaq = await obtenerPreguntasFrecuentes();
        const historial = await obtenerHistorialPorNumero(ctx.from);

        const textoDoctores = horariosDoctores.map(d => {
            return `👨‍⚕️ *${d.nombre}* (${d.especialidad}):\n${d.horarios.map(h => `  - ${h.dias}: ${h.desde} a ${h.hasta} ${h.precio ? `💰 Precio: ${h.precio} Bs.` : ''}`).join('\n')}`;
        }).join('\n\n');

        const textoHistorial = historial.length > 0
            ? historial.map((item, i) => `${i + 1}.${item.answer}`).join('\n')
            : 'Este usuario no tiene historial previo.';
        // console.log(textoHistorial)
        // const textoHistorial= ''

        const mensageNew = ctx.body.toLowerCase();
        const regex = /[^\w\s]/g;
        const mensajeNuevo = mensageNew.replace(regex, '').replace(/\s+/g, ' ').trim();

        const textoPreguntas = preguntasFaq.map(p => {
            let texto = `❓ ${p.pregunta}\n💬 ${p.respuesta}`;
            if (p.precio && !isNaN(p.precio)) {
                texto += `\n💰 Precio: ${p.precio} Bs.`;
            }
            return texto;
        }).join('\n\n');

        const prompt = `
Eres Naty, la asistente virtual de Clínica Natividad siempre debes responder de manera profesional y amigable. Tu objetivo es ayudar a los usuarios con sus preguntas sobre salud, doctores, horarios, emergencias y más.
Historial del usuario:
${textoHistorial}

Preguntas frecuentes:
${textoPreguntas}

Mensaje recibido del usuario: "${mensajeNuevo}"

Horarios de doctores:
${textoDoctores}

Tareas:
- Detecta síntomas y sugiere especialidades.
- Muestra horarios si se pregunta por un doctor específico.
- Si se pregunta por dirección, contacto, emergencias, responde adecuadamente.
- Se empático y profesional en todas las respuestas.
- el precio siempre debes lostrarlo de "Preguntas frecuentes:"
- Cuando menciones un precio, asegúrate de usar el formato "💰 Precio: 000 Bs."
- Si el usuario pide agendar, dile: "👌 ¡Perfecto! Te agendaré, por favor espera un momento que un personal se contactara con usted.
`;
        console.log('Prompt enviado a Gemini:', prompt);
        const respuestaGemini = await consultarGemini(prompt);
        await flowDynamic(respuestaGemini);
    })

// Inicialización del bot
const main = async () => {
    const adapterDB = new MySQLAdapter({
        host: MYSQL_DB_HOST,
        user: MYSQL_DB_USER,
        database: MYSQL_DB_NAME,
        password: MYSQL_DB_PASSWORD,
        port: MYSQL_DB_PORT,
    });

    const adapterFlow = createFlow([
        flowNaty
    ]);

    const adapterProvider = createProvider(BaileysProvider);

    createBot({
        flow: adapterFlow,
        provider: adapterProvider,
        database: adapterDB,
    });

    QRPortalWeb({ port: process.env.PORT || 3100 });
};

main();
