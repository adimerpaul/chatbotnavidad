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



// Flujo de bienvenida
const flowBienvenida = addKeyword(EVENTS.WELCOME)
    .addAnswer('💧 ¡Hola! Soy *Yaku*, la asistente virtual de *SELA Oruro*.')
    .addAnswer('Por favor, envíame tu *número de cuenta* para verificar tu estado de deuda. Ejemplo: 2255749.');

// Flujo general
const flowNaty = addKeyword([], { events: [EVENTS.MESSAGE] })
    .addAction(async (ctx, { flowDynamic, provider }) => {
        // ESPECIALIDAD	DOCTOR	DESDE	HASTA	DIAS
        // ESPECIALIDADES BÁSICAS
        // CIRUGIA	TOLA BAUTISTA JUAN CARLOS	09:00	11:00	MARTES A VIERNES
        // 20:00	21:00	LUNES A JUEVES
        // CALLAPA ZAMBRANA MICHAEL	08:00	10:00	LUNES A VIERNES
        // 14:00	16:00	LUNES A VIERNES
        // JUANIQUINA VALDEZ ALBERT	10:30	12:30	LUNES A VIERNES
        // GINECOLOGIA	ARELLANO ALANEZ MIGUEL	09:00	11:00	LUNES A VIERNES
        // MAMANI MAMANI VIDAL	11:00	13:00	LUNES A VIERNES
        // CONDARCO HUALLARTE KENIA	15:00	18:00	LUNES A VIERNES
        // MEDICINA INTERNA	ORTIZ ORTEGA JOSE	11:30	13:30	LUNES, MARTES, JUEVES Y VIERNES
        // 16:30	18:30	LUNES, MARTES, JUEVES Y VIERNES
        // ARANCIBIA VELASCO DANIEL	14:30	16:30	LUNES A JUEVES
        // CHECA REVILLA ALVARO	09:30	11:30	LUNES A VIERNES
        // PEDIATRIA	GORDILLO ABSAEL 	09:30	11:30	LUNES A JUEVES
        // 11:00	12:00	SABADOS
        // VERA BETZI	15:00	18:00	LUNES. MARTES. MIERCOLES Y VIERNES
        // TUPA LIMA REYNALDO	19:30	21:00	LUNES A VIERNES
        // TRAUMATOLOGIA	AVENDAÑO OSCAR	11:00	13:00	LUNES, MIERCOLES JUEVES Y VIERNES
        // LAZARTE LUIS	09:00	11:00	LUNES A VIERNES
        // MARTINEZ RAFAEL	14:00	16:30	LUNES A VIERNES
        // ESPECIALIDADES MÉDICAS
        // CARDIOLOGIA	ARANDIA LIMACHE WILDSOR	11:00	12:30	LUNES A VIERNES
        // HONORIO Q. CARMEN	16:00	18:00	LUNES, MIERCOLES Y VIERNES
        // CIRUGIA DE CABEZA Y CUELLO	HURTADO AVILES MARCO	10:00	12:00	LUNES, MIERCOLES Y VIERNES
        // CIRUGIA CARDIOVASCULAR	IQUISE ILACIO VICTOR	14:30	15:30	LUNES, MIERCOLES Y VIERNES
        // CIRUGIA VASCULAR	GUTIERREZ JUAN CARLOS	15:00	16:00	A LLAMADO
        // COLOPROCTOLOGIA	COLQUE MARDÍN ALEX	08:00	09:00	MARTES A VIERNES
        // DERMATOLOGIA	ARANIBAR BENITO  PAOLA	16:30	18:30	LUNES A VIERNES
        // ENDOCRINOLOGIA	ESCALERA PAQUI JORGE	09:00	11:00	MARTES, JUEVES
        // 20:30	21:30	LUNES, MIERCOLES Y JUEVES
        // GASTROENTEROLOGIA	LLANQUE MAMANI RODRIGO	10:00	12:00	LUNES A VIERNES
        // VILLEGAS YAÑEZ JOSE LUIS	16:30	19:30	LUNES A VIERNES
        // GERIATRIA	MENDOZA MORA BRESNEV	14:00	15:00	LUNES Y JUEVES
        // HEMATOLOGIA	MENESES HIDALGO ERIC	12:00	14:00	MARTES Y JUEVES
        // 17:00	19:00	LUNES, MIERCOLES Y VIERNES
        // NEFROLOGIA	CACERES PEREYRA CARLOS	15:00	16:00	LUNES A VIERNES
        // NEUMOLOGIA	RODRIGUEZ RENJEL MARIA	18:30	-	LUNES A VIERNES
        // NEUROCIRUGIA	AMELLER RONALD	17:00	18:00	LUNES A VIERNES
        // CHUQUIMIA RODRIGUEZ ADAN	09:00	11:00	LUNES A VIERNES
        // PRADO NITHZE	17:00	19:00	LUNES A VIERNES
        // NEUROLOGIA CLINICA	QUILO IGOR	A LLAMADO		LUNES Y MARTES
        // POZO MILENNA	17:00	19:00	LUNES A VIERNES
        // NUTRICION	MEDRANO KATIA	14:00	15:00	LUNES A VIERNES
        // ONCOLOGIA	CESPEDES ZENON	16:00	18:00	LUNES A JUEVES
        // PSICOLOGIA	RAMIREZ ANDREA	16:00	17:00	MARTES, JUEVES Y VIERNES
        // RODRIGUEZ NADIR	16:00	19:00	LUNES
        // PSIQUIATRIA	BUSTOS DAVID	A LLAMADO		A LLAMADO
        // REUMATOLOGIA	LUCANA LINA	A LLAMADO		LUNES A VIERNES
        // MAMANI MARIANA	09:00	10:30	MIERCOLES
        // 11:00	13:00	MARTES Y JUEVES
        // UROLOGIA	CALDERON ALBERTO	11:30	12:30	MARTES, MIERCOLES Y VIERNES
        // 19:00	-	JUEVES
        // SUB-ESPECIALIDADES MEDICAS
        // CARDIOLOGIA PEDIATRICA	SANCHEZ ROSALIA	17:00	19:00	LUNES, MIÉRCOLES Y VIERNES
        // ENDOCRINOLOGIA PEDIATRICA	MAMANI ROLY	A LLAMADO		A LLAMADO
        // NEUROLOGIA PEDIATRICA	HERRERA VERONICA	09:00	11:00	SABADOS
        // NEONATOLOGIA Y UTI NEONATAL	HERRERA LILY	A REQUERIMIENTO		A REQUERIMIENTO
        // TUPA REYNALDO
        // TRAUMATOLOGIA PEDIATRICA	TAPIA GUIDO	15:00	17:00	LUNES, MIERCOLES Y VIERNES
        // UTI - PEDIATRICA	VERA BETZI	A REQUERIMIENTO		A REQUERIMIENTO


        horariosDoctores = [
            {
                "nombre": "TOLA BAUTISTA JUAN CARLOS",
                "especialidad": "CIRUGIA",
                "horarios": [
                    { "desde": "09:00", "hasta": "11:00", "dias": "MARTES A VIERNES" },
                    { "desde": "20:00", "hasta": "21:00", "dias": "LUNES A JUEVES" }
                ]
            },
            {
                "nombre": "CALLAPA ZAMBRANA MICHAEL",
                "especialidad": "CIRUGIA",
                "horarios": [
                    { "desde": "08:00", "hasta": "10:00", "dias": "LUNES A VIERNES" },
                    { "desde": "14:00", "hasta": "16:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "JUANIQUINA VALDEZ ALBERT",
                "especialidad": "CIRUGIA",
                "horarios": [
                    { "desde": "10:30", "hasta": "12:30", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "ARELLANO ALANEZ MIGUEL",
                "especialidad": "GINECOLOGIA",
                "horarios": [
                    { "desde": "09:00", "hasta": "11:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "MAMANI MAMANI VIDAL",
                "especialidad": "GINECOLOGIA",
                "horarios": [
                    { "desde": "11:00", "hasta": "13:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "CONDARCO HUALLARTE KENIA",
                "especialidad": "GINECOLOGIA",
                "horarios": [
                    { "desde": "15:00", "hasta": "18:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "ORTIZ ORTEGA JOSE",
                "especialidad": "MEDICINA INTERNA",
                "horarios": [
                    { "desde": "11:30", "hasta": "13:30", "dias": "LUNES, MARTES, JUEVES Y VIERNES" },
                    { "desde": "16:30", "hasta": "18:30", "dias": "LUNES, MARTES, JUEVES Y VIERNES" }
                ]
            },
            {
                "nombre": "ARANCIBIA VELASCO DANIEL",
                "especialidad": "MEDICINA INTERNA",
                "horarios": [
                    { "desde": "14:30", "hasta": "16:30", "dias": "LUNES A JUEVES" }
                ]
            },
            {
                "nombre": "CHECA REVILLA ALVARO",
                "especialidad": "MEDICINA INTERNA",
                "horarios": [
                    { "desde": "09:30", "hasta": "11:30", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "GORDILLO ABSAEL",
                "especialidad": "PEDIATRIA",
                "horarios": [
                    { "desde": "09:30", "hasta": "11:30", "dias": "LUNES A JUEVES" },
                    { "desde": "11:00", "hasta": "12:00", "dias": "SABADOS" }
                ]
            },
            {
                "nombre": "VERA BETZI",
                "especialidad": "PEDIATRIA",
                "horarios": [
                    { "desde": "15:00", "hasta": "18:00", "dias": "LUNES, MARTES, MIERCOLES Y VIERNES" }
                ]
            },
            {
                "nombre": "TUPA LIMA REYNALDO",
                "especialidad": "PEDIATRIA",
                "horarios": [
                    { "desde": "19:30", "hasta": "21:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "AVENDAÑO OSCAR",
                "especialidad": "TRAUMATOLOGIA",
                "horarios": [
                    { "desde": "11:00", "hasta": "13:00", "dias": "LUNES, MIERCOLES, JUEVES Y VIERNES" }
                ]
            },
            {
                "nombre": "LAZARTE LUIS",
                "especialidad": "TRAUMATOLOGIA",
                "horarios": [
                    { "desde": "09:00", "hasta": "11:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "MARTINEZ RAFAEL",
                "especialidad": "TRAUMATOLOGIA",
                "horarios": [
                    { "desde": "14:00", "hasta": "16:30", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "ARANDIA LIMACHE WILDSOR",
                "especialidad": "CARDIOLOGIA",
                "horarios": [
                    { "desde": "11:00", "hasta": "12:30", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "HONORIO Q. CARMEN",
                "especialidad": "CARDIOLOGIA",
                "horarios": [
                    { "desde": "16:00", "hasta": "18:00", "dias": "LUNES, MIERCOLES Y VIERNES" }
                ]
            },
            {
                "nombre": "HURTADO AVILES MARCO",
                "especialidad": "CIRUGIA DE CABEZA Y CUELLO",
                "horarios": [
                    { "desde": "10:00", "hasta": "12:00", "dias": "LUNES, MIERCOLES Y VIERNES" }
                ]
            },
            {
                "nombre": "IQUISE ILACIO VICTOR",
                "especialidad": "CIRUGIA CARDIOVASCULAR",
                "horarios": [
                    { "desde": "14:30", "hasta": "15:30", "dias": "LUNES, MIERCOLES Y VIERNES" }
                ]
            },
            {
                "nombre": 'GUTIERREZ JUAN CARLOS',
                'especialidad': 'CIRUGIA VASCULAR',
                'horarios': [
                    { 'desde': '15:00', 'hasta': '16:00', 'dias': 'A LLAMADO' }
                ]
            },
            {
                "nombre": "COLQUE MARDÍN ALEX",
                "especialidad": "COLOPROCTOLOGIA",
                "horarios": [
                    { "desde": "08:00", "hasta": "09:00", "dias": "MARTES A VIERNES" }
                ]
            },
            {
                "nombre": "ARANIBAR BENITO PAOLA",
                "especialidad": "DERMATOLOGIA",
                "horarios": [
                    { "desde": "16:30", "hasta": "18:30", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "ESCALERA PAQUI JORGE",
                "especialidad": "ENDOCRINOLOGIA",
                "horarios": [
                    { "desde": "09:00", "hasta": "11:00", "dias": "MARTES, JUEVES" },
                    { "desde": "20:30", "hasta": "21:30", "dias": "LUNES, MIERCOLES Y JUEVES" }
                ]
            },
            {
                "nombre": 'LLANQUE MAMANI RODRIGO',
                'especialidad': 'GASTROENTEROLOGIA',
                'horarios': [
                    { 'desde': '10:00', 'hasta': '12:00', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                'nombre': 'VILLEGAS YAÑEZ JOSE LUIS',
                'especialidad': 'GASTROENTEROLOGIA',
                'horarios': [
                    { 'desde': '16:30', 'hasta': '19:30', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                'nombre': 'MENDOZA MORA BRESNEV',
                'especialidad': 'GERIATRIA',
                'horarios': [
                    { 'desde': '14:00', 'hasta': '15:00', 'dias': 'LUNES Y JUEVES' }
                ]
            },
            {
                'nombre': 'MENESES HIDALGO ERIC',
                'especialidad': 'HEMATOLOGIA',
                'horarios': [
                    { 'desde': '12:00', 'hasta': '14:00', 'dias': 'MARTES Y JUEVES' },
                    { 'desde': '17:00', 'hasta': '19:00', 'dias': 'LUNES, MIERCOLES Y VIERNES' }
                ]
            },
            {
                'nombre': 'CACERES PEREYRA CARLOS',
                'especialidad': 'NEFROLOGIA',
                'horarios': [
                    { 'desde': '15:00', 'hasta': '16:00', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                'nombre': 'RODRIGUEZ RENJEL MARIA',
                'especialidad': 'NEUMOLOGIA',
                'horarios': [
                    { 'desde': '18:30', 'hasta': '-', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                'nombre': 'AMELLER RONALD',
                'especialidad': 'NEUROCIRUGIA',
                'horarios': [
                    { 'desde': '17:00', 'hasta': '18:00', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                'nombre': 'CHUQUIMIA RODRIGUEZ ADAN',
                'especialidad': 'NEUROCIRUGIA',
                'horarios': [
                    { 'desde': '09:00', 'hasta': '11:00', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                "nombre": "PRADO NITHZE",
                "especialidad": "NEUROCIRUGIA",
                "horarios": [
                    { "desde": "17:00", "hasta": "19:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "QUILO IGOR",
                "especialidad": "NEUROLOGIA CLINICA",
                "horarios": [
                    { "desde": "A LLAMADO", "hasta": "-", "dias": "LUNES Y MARTES" }
                ]
            },
            {
                "nombre": "POZO MILENNA",
                "especialidad": "NEUROLOGIA CLINICA",
                "horarios": [
                    { "desde": "17:00", "hasta": "19:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "MEDRANO KATIA",
                "especialidad": "NUTRICION",
                "horarios": [
                    { "desde": "14:00", "hasta": "15:00", "dias": "LUNES A VIERNES" }
                ]
            },
            {
                "nombre": "CESPEDES ZENON",
                "especialidad": "ONCOLOGIA",
                "horarios": [
                    { "desde": "16:00", "hasta": "18:00", "dias": "LUNES A JUEVES" }
                ]
            },
            {
                "nombre": "RAMIREZ ANDREA",
                "especialidad": "PSICOLOGIA",
                "horarios": [
                    { "desde": "16:00", "hasta": "17:00", "dias": "MARTES, JUEVES Y VIERNES" }
                ]
            },
            {
                "nombre": "RODRIGUEZ NADIR",
                "especialidad": "PSIQUIATRIA",
                "horarios": [
                    { "desde": "16:00", "hasta": "19:00", "dias": "LUNES" }
                ]
            },
            {
                'nombre': 'BUSTOS DAVID',
                'especialidad': 'PSIQUIATRIA',
                'horarios': [
                    { 'desde': 'A LLAMADO', 'hasta': '-', 'dias': 'A LLAMADO' }
                ]
            },
            {
                'nombre': 'LUCANA LINA',
                'especialidad': 'REUMATOLOGIA',
                'horarios': [
                    { 'desde': 'A LLAMADO', 'hasta': '-', 'dias': 'LUNES A VIERNES' }
                ]
            },
            {
                'nombre': 'MAMANI MARIANA',
                'especialidad': 'REUMATOLOGIA',
                'horarios': [
                    { 'desde': '09:00', 'hasta': '10:30', 'dias': 'MIERCOLES' },
                    { 'desde': '11:00', 'hasta': '13:00', 'dias': 'MARTES Y JUEVES' }
                ]
            },
            {
                "nombre": "CALDERON ALBERTO",
                "especialidad": "UROLOGIA",
                "horarios": [
                    { "desde": "11:30", "hasta": "12:30", "dias": "MARTES, MIERCOLES Y VIERNES" },
                    { "desde": "19:00", "hasta": "-", "dias": "JUEVES" }
                ]
            },
            {
                "nombre": "SANCHEZ ROSALIA",
                "especialidad": "CARDIOLOGIA PEDIATRICA",
                "horarios": [
                    { "desde": "17:00", "hasta": "-", "dias": "LUNES, MIERCOLES Y VIERNES" }
                ]
            },
            {
                "nombre": "MAMANI ROLY",
                "especialidad": "ENDOCRINOLOGIA PEDIATRICA",
                "horarios": [
                    { "desde": "A LLAMADO", "hasta": "-", "dias": "A LLAMADO" }
                ]
            },
            {
                "nombre": "HERRERA VERONICA",
                "especialidad": "NEUROLOGIA PEDIATRICA",
                "horarios": [
                    { "desde": "09:00", "hasta": "11:00", "dias": "SABADOS" }
                ]
            },
            {
                'nombre': 'HERRERA LILY',
                'especialidad': 'NEONATOLOGIA Y UTI NEONATAL',
                'horarios': [
                    { 'desde': 'A REQUERIMIENTO', 'hasta': '-', 'dias': 'A REQUERIMIENTO' }
                ]
            },
            {
                'nombre': 'TAPIA GUIDO',
                'especialidad': 'TRAUMATOLOGIA PEDIATRICA',
                'horarios': [
                    { 'desde': '15:00', 'hasta': '17:00', 'dias': 'LUNES, MIERCOLES Y VIERNES' }
                ]
            },
            {
                'nombre': 'VERA BETZI',
                'especialidad': 'UTI - PEDIATRICA',
                'horarios': [
                    { 'desde': 'A REQUERIMIENTO', 'hasta': '-', 'dias': 'A REQUERIMIENTO' }
                ]
            }
        ]

        const buscarDoctor = (nombre) => {
            return horariosDoctores.find(d => d.nombre.toLowerCase().includes(nombre.toLowerCase()))
        }
        const historial = await obtenerHistorialPorNumero(ctx.from);
        const textoHistorial = historial.length > 0
            ? historial.map((item, i) => `${i + 1}. ${item.keyword} → ${item.answer}`).join('\n')
            : 'Este usuario no tiene historial previo.';

        const prompt = `
Eres Naty, la asistente virtual de Clínica Natividad. Tu tarea es analizar el siguiente mensaje de WhatsApp y responder amable mente

Historial del usuario:
${textoHistorial}

Mensaje recibido del usuario: "${ctx.body}"

Objetivo:
- Detectar si el usuario tiene un síntoma o malestar.
- Sugerir una o dos especialidades médicas (Medicina General, Cirugía, Anestesiología, Cardiología, Cirugía Plástica, Coloproctología, Dermatología, Ecografía, Fisioterapia, Gastroenterología, Ginecología, Hematología,Imagenología).
- Simular el proceso de agendamiento si el usuario lo solicita.
- Sé amable, empática y profesional.
- Si pregunta por teléfonos de emergencia → intencion: "emergencia", respuesta: "📞 Números de emergencia: 78610575, 77888844, 52-54721, 52-83667"
- Si pregunta por dirección, teléfono fijo, email o WhatsApp → intencion: "info_contacto", respuesta: "📍 Clínica Natividad SRL: Bolívar 753 Arica e Iquique, Oruro - Bolivia. Tel: 52-54721. Email: clinicanatividad@grupo-plaza.com"
si pregunta por doctores → intencion: "horarios_doctores", respuesta: "Aquí tienes los horarios de nuestros doctores:\n\n${horariosDoctores.map(d => `👨‍⚕️ *${d.nombre}* (${d.especialidad}):\n${d.horarios.map(h => `  - ${h.dias}: ${h.desde} a ${h.hasta}`).join('\n')}`).join('\n\n')}\n\n Si deseas agendar una cita, por favor indícanos el nombre del doctor y la fecha que prefieres."
y si busca un doctor específico solo responde con el nombre del doctor y la especialidad, por ejemplo: "Tola Bautista Cirugía" o "Callapa Zambrana Cirugía".
`;

        const respuestaGemini = await consultarGemini(prompt);
        console.log('respuestaGemini: ', respuestaGemini);
        // console.log('↩️ Respuesta bruta de Gemini:\n', respuestaGemini);
        await flowDynamic(respuestaGemini);


        // const jsonString = respuestaGemini.match(/{[\s\S]*}/)[0];
        //
        // // ---- Extracción del JSON (tu código actual) ----
        // // Lo he simplificado un poco para que sea más robusto
        // // let res;
        // // try {
        // //     // Intenta encontrar un bloque JSON, incluso sin los ```
        // //     const jsonString = respuestaGemini.match(/{[\s\S]*}/)[0];
        // //     res = JSON.parse(jsonString);
        // //     if (!res || typeof res !== 'object' || !res.intencion) {
        // //         await flowDynamic('⚠️ Lo siento, no entendí tu mensaje. ¿Puedes repetirlo o enviarme tu número de cuenta?');
        // //         return;
        // //     }
        // // } catch (error) {
        // //     console.error('❌ Error al parsear el JSON de Gemini:', error.message);
        // //     // Si falla el parseo, enviamos una respuesta genérica en lugar de fallar silenciosamente.
        // //     // A veces Gemini responde con texto plano si el prompt no es claro.
        // //     await flowDynamic(respuestaGemini);
        // //     return;
        // // }
        //
        // // ---- Lógica para decidir qué hacer ----
        //
        // // CASO 1: El usuario quiere saber dónde está su número de cuenta
        // // if (res.intencion === 'mostrar_ubicacion_cuenta') {
        // //     await flowDynamic([
        // //         {
        // //             body: res.respuesta, // "Claro, puedes encontrar tu número de cuenta aquí:"
        // //             media: 'https://selaoruro.gob.bo/img/chat/cuenta.png', // <-- URL de tu imagen
        // //         },
        // //     ]);
        // //     return; // Termina el flujo aquí
        // // }
        //
        // // CASO 3: Cualquier otra respuesta de texto (pedir la cuenta, info de contacto, etc.)
        // // await flowDynamic(res.respuesta);
        // const jsonString = match[0];
        // res = JSON.parse(jsonString);
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
