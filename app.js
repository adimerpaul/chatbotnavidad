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

// Función para consultar historial
async function obtenerHistorialPorNumero(numero) {
    const connection = await mysql.createConnection({
        host: MYSQL_DB_HOST,
        user: MYSQL_DB_USER,
        database: MYSQL_DB_NAME,
        password: MYSQL_DB_PASSWORD,
        port: MYSQL_DB_PORT,
    });

    const [rows] = await connection.execute(
        'SELECT keyword, answer, created_at FROM history WHERE phone = ? ORDER BY created_at DESC LIMIT 5',
        [numero]
    );

    await connection.end();
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

// Función para consultar deuda vía API externa
function formatarPeriodo(periodoStr) {
    const anio = periodoStr.substring(0, 4);
    const mesNum = periodoStr.substring(4, 6);
    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    // El índice del array es mesNum - 1
    const nombreMes = meses[parseInt(mesNum, 10) - 1];
    return `${nombreMes} ${anio}`;
}


// Función para consultar deuda vía API externa (Versión mejorada)
async function consultarDeudaPorCuenta(cuenta) {

    const apiBaseUrl = process.env.DEBT_API_URL; // <-- PASO 5
    const url = `${apiBaseUrl}?cuenta=${cuenta}`;

    try {
        const response = await axios.get(url);
        const datos = response.data;
        console.log(datos)

        // Caso 1: La API indica que la operación no fue exitosa (ej. cuenta no existe)
        if (!datos.success) {
            // {
            //     success: false,
            //         message: 'Este número de cuenta no tiene deudas pendientes. Revise el número de cuenta e intente nuevamente',
            //     cliente: {
            //     nombre: 'MONTALVO CESPEDES MARIO',
            //         ci: '554592',
            //         cuenta: '2255749'
            // }
            if (datos.cliente != null) {
                return `✅ La cuenta *${cuenta}* a nombre de *${datos.cliente.nombre}* no tiene deudas pendientes.`;
            }
            return `⚠️ ${datos.message || `No se encontró información para la cuenta ${cuenta}.`}`;
        }

        // Extraemos las partes importantes de la respuesta
        const { cliente, facturas, otrosIngresos, corte_realizado } = datos.factura;

        // Caso 2: La cuenta existe pero no tiene facturas registradas.
        if (!facturas || facturas.length === 0) {
            return `✅ ¡Buenas noticias! La cuenta *${cuenta}* a nombre de *${cliente.nombre}* no tiene deudas pendientes.`;
        }

        // Empezamos a construir el mensaje de respuesta
        let mensaje = [];
        mensaje.push(`📄 *Detalle de Deuda para la Cuenta ${cuenta}*`);
        mensaje.push(`👤 *Cliente:* ${cliente.nombre}`);
        mensaje.push(`🏠 *Dirección:* ${cliente.direccion}`);
        mensaje.push(``); // Línea en blanco para espaciar

        // Caso 3: Detectar si el servicio está cortado y añadir una alerta.
        if (corte_realizado && corte_realizado.corte && corte_realizado.corte.fecha_corte) {
            const fechaCorte = new Date(corte_realizado.corte.fecha_corte).toLocaleDateString('es-ES', {
                year: 'numeric', month: 'long', day: 'numeric'
            });
            mensaje.push(`🚨 *¡ATENCIÓN!* 🚨`);
            mensaje.push(`_Su servicio fue cortado el ${fechaCorte}._`);
            mensaje.push(`_Para la rehabilitación, debe cancelar la totalidad de la deuda._`);
            mensaje.push(``);
        }

        let totalFacturas = 0;

        // Procesar las facturas pendientes
        mensaje.push('*Facturas Pendientes:*');
        facturas.forEach(factura => {
            const monto = parseFloat(factura.monto);
            mensaje.push(`  - ${formatarPeriodo(factura.periodo)}: *Bs. ${monto.toFixed(2)}*`);
            totalFacturas += monto;
        });

        let totalOtrosIngresos = 0;

        // Procesar otros ingresos si existen
        if (otrosIngresos && otrosIngresos.length > 0) {
            mensaje.push(``);
            mensaje.push('*Otros Cargos:*');
            otrosIngresos.forEach(cargo => {
                const valor = parseFloat(cargo.valor);
                mensaje.push(`  - ${cargo.concepto}: *Bs. ${valor.toFixed(2)}*`);
                totalOtrosIngresos += valor;
            });
        }

        // Calcular y mostrar el total final
        const deudaTotal = totalFacturas + totalOtrosIngresos;
        mensaje.push(``);
        mensaje.push(`*DEUDA TOTAL A PAGAR: Bs. ${deudaTotal.toFixed(2)}*`);

        return mensaje.join('\n');

    } catch (error) {
        console.error('Error al consultar la deuda:', error.response ? error.response.data : error.message);
        return '⚠️ Lo siento, ocurrió un problema al conectar con el sistema de deudas. Por favor, intenta de nuevo más tarde.';
    }
}


// Flujo de bienvenida
const flowBienvenida = addKeyword(EVENTS.WELCOME)
    .addAnswer('💧 ¡Hola! Soy *Yaku*, la asistente virtual de *SELA Oruro*.')
    .addAnswer('Por favor, envíame tu *número de cuenta* para verificar tu estado de deuda. Ejemplo: 2255749.');

// Flujo general
const flowNaty = addKeyword([], { events: [EVENTS.MESSAGE] })
    .addAction(async (ctx, { flowDynamic, provider }) => {
        // const mensaje = ctx.body.trim();
        const historial = await obtenerHistorialPorNumero(ctx.from);
        const textoHistorial = historial.length > 0
            ? historial.map((item, i) => `${i + 1}. ${item.keyword} → ${item.answer}`).join('\n')
            : 'Este usuario no tiene historial previo.';

        const prompt = `
Eres Yaku, la asistente virtual de SELA Oruro. Tu tarea es analizar el siguiente mensaje de WhatsApp y responder únicamente con un JSON válido.

Historial del usuario:
${textoHistorial}

Mensaje recibido del usuario: "${ctx.body}"

Tu objetivo es generar un JSON con esta estructura:
{
  "mandoCuenta": boolean,              // true si el usuario envió su número de cuenta (aunque esté separado)
  "cuenta": string | null,             // número de cuenta extraído, o null si no se envió
  "respuesta": string,                 // texto claro y amable que responderás al usuario
  "intencion": "mostrar_ubicacion_cuenta" | "instalacion" | "nombre" | "consulta_deuda" | "info_contacto" | "otro"
}

Reglas:
- Si el usuario pregunta DÓNDE encontrar su número de cuenta → intencion: "mostrar_ubicacion_cuenta" y la respueta debe ter que la cuenta se encuentra en la factura o aviso de cobro como muestra en la imagen.
- Si pregunta cómo iniciar una NUEVA INSTALACIÓN → intencion: "instalacion".
- Si pregunta cómo CAMBIAR DE NOMBRE → intencion: "nombre".
- Si menciona RECLAMOS, CORTE, DEUDA, MEDIDOR o CONSULTA relacionada al servicio → intencion: "consulta_deuda".
- Si solicita dirección, teléfono o WhatsApp →  Av. Villarroel Nro 222 entre Brasil y Backovic Oruro - Bolivia Telefono: (2) 5235947 Whatsapp: 71880887
- Si menciona en qué bancos puede pagar, responde que puede hacerlo en: las siguientes entidades financieras habilitadas son banco_sol, fie, progreso, paulo_sexto, promujer, ecofuturo y bnb.
- Si no manda el número de cuenta, pide amablemente que lo envíe (puede estar separado por espacios).
- Si el usuario sí envió el número, pon "mandoCuenta": true y llena correctamente el campo "cuenta".
- No agregues texto adicional ni explicaciones. Solo responde con el JSON plano, sin backticks.
`;

        const respuestaGemini = await consultarGemini(prompt);
        console.log('respuestaGemini: ', respuestaGemini);

        // ---- Extracción del JSON (tu código actual) ----
        // Lo he simplificado un poco para que sea más robusto
        let res;
        try {
            // Intenta encontrar un bloque JSON, incluso sin los ```
            const jsonString = respuestaGemini.match(/{[\s\S]*}/)[0];
            res = JSON.parse(jsonString);
            if (!res || typeof res !== 'object' || !res.intencion) {
                await flowDynamic('⚠️ Lo siento, no entendí tu mensaje. ¿Puedes repetirlo o enviarme tu número de cuenta?');
                return;
            }
        } catch (error) {
            console.error('❌ Error al parsear el JSON de Gemini:', error.message);
            // Si falla el parseo, enviamos una respuesta genérica en lugar de fallar silenciosamente.
            // A veces Gemini responde con texto plano si el prompt no es claro.
            await flowDynamic(respuestaGemini);
            return;
        }

        // ---- Lógica para decidir qué hacer ----

        // CASO 1: El usuario quiere saber dónde está su número de cuenta
        if (res.intencion === 'mostrar_ubicacion_cuenta') {
            await flowDynamic([
                {
                    body: res.respuesta, // "Claro, puedes encontrar tu número de cuenta aquí:"
                    media: 'https://selaoruro.gob.bo/img/chat/cuenta.png', // <-- URL de tu imagen
                },
            ]);
            return; // Termina el flujo aquí
        }
        // CASO 2: El usuario quiere saber cómo iniciar una nueva instalación
        if (res.intencion === 'instalacion') {
            await flowDynamic([
                {
                    body: res.respuesta,
                    media: 'https://selaoruro.gob.bo/img/chat/instalacion.jpg',
                }
            ])
            return; // Termina el flujo aquí
        }
        // CASO 3: El usuario quiere saber cómo realizar un cambio de nombre
        if (res.intencion === 'nombre') {
            await flowDynamic([
                {
                    body: res.respuesta,
                    media: 'https://selaoruro.gob.bo/img/chat/cambionombre.jpg',
                }
            ]);
            return; // Termina el flujo aquí
        }

        // CASO 2: El usuario mandó su número de cuenta
        if (res.mandoCuenta) {
            await flowDynamic('Consultando, un momento por favor... ⏳');
            const respuestaDeuda = await consultarDeudaPorCuenta(res.cuenta);
            await flowDynamic(respuestaDeuda);
            return; // Termina el flujo aquí
        }

        // CASO 3: Cualquier otra respuesta de texto (pedir la cuenta, info de contacto, etc.)
        await flowDynamic(res.respuesta);
    });

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
        flowBienvenida,
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
