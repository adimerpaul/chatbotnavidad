<?php

namespace Database\Seeders;

use App\Models\Asignacion;
use App\Models\AsignacionEstudiante;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Estudiante;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void{
        $user = User::create([
            'name' => 'Gabriela Echeverria',
            'username' => 'admin',
//            'avatar' => 'default.png',
//            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123Admin'),
            'role' => 'Administrador',
        ]);
        $doctores = [
            [
                'name' => 'TOLA BAUTISTA JUAN CARLOS',
                'specialty' => 'CIRUGIA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '11:00', 'days' => 'MARTES A VIERNES', 'price' => 150],
                    ['available_from' => '20:00', 'available_to' => '21:00', 'days' => 'LUNES A JUEVES', 'price' => 150],
                ]
            ],
            [
                'name' => 'CALLAPA ZAMBRANA MICHAEL',
                'specialty' => 'CIRUGIA',
                'schedules' => [
                    ['available_from' => '08:00', 'available_to' => '10:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                    ['available_from' => '14:00', 'available_to' => '16:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'JUANIQUINA VALDEZ ALBERT',
                'specialty' => 'CIRUGIA',
                'schedules' => [
                    ['available_from' => '10:30', 'available_to' => '12:30', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'ARELLANO ALANEZ MIGUEL',
                'specialty' => 'GINECOLOGIA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '11:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'MAMANI MAMANI VIDAL',
                'specialty' => 'GINECOLOGIA',
                'schedules' => [
                    ['available_from' => '11:00', 'available_to' => '13:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'CONDARCO HUALLARTE KENIA',
                'specialty' => 'GINECOLOGIA',
                'schedules' => [
                    ['available_from' => '15:00', 'available_to' => '18:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'ORTIZ ORTEGA JOSE',
                'specialty' => 'MEDICINA INTERNA',
                'schedules' => [
                    ['available_from' => '11:30', 'available_to' => '13:30', 'days' => 'LUNES, MARTES, JUEVES Y VIERNES', 'price' => 200],
                    ['available_from' => '16:30', 'available_to' => '18:30', 'days' => 'LUNES, MARTES, JUEVES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'ARANCIBIA VELASCO DANIEL',
                'specialty' => 'MEDICINA INTERNA',
                'schedules' => [
                    ['available_from' => '14:30', 'available_to' => '16:30', 'days' => 'LUNES A JUEVES', 'price' => 200],
                ]
            ],
            [
                'name' => 'CHECA REVILLA ALVARO',
                'specialty' => 'MEDICINA INTERNA',
                'schedules' => [
                    ['available_from' => '09:30', 'available_to' => '11:30', 'days' => 'LUNES A VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'GORDILLO ABSAEL',
                'specialty' => 'PEDIATRIA',
                'schedules' => [
                    ['available_from' => '09:30', 'available_to' => '11:30', 'days' => 'LUNES A JUEVES', 'price' => 150],
                    ['available_from' => '11:00', 'available_to' => '12:00', 'days' => 'SABADOS', 'price' => 150],
                ]
            ],
            [
                'name' => 'VERA BETZI',
                'specialty' => 'PEDIATRIA',
                'schedules' => [
                    ['available_from' => '15:00', 'available_to' => '18:00', 'days' => 'LUNES, MARTES, MIERCOLES Y VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'TUPA LIMA REYNALDO',
                'specialty' => 'PEDIATRIA',
                'schedules' => [
                    ['available_from' => '19:30', 'available_to' => '21:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'AVENDAÑO OSCAR',
                'specialty' => 'TRAUMATOLOGIA',
                'schedules' => [
                    ['available_from' => '11:00', 'available_to' => '13:00', 'days' => 'LUNES, MIERCOLES JUEVES Y VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'LAZARTE LUIS',
                'specialty' => 'TRAUMATOLOGIA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '11:00', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'MARTINEZ RAFAEL',
                'specialty' => 'TRAUMATOLOGIA',
                'schedules' => [
                    ['available_from' => '14:00', 'available_to' => '16:30', 'days' => 'LUNES A VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'ARANDIA LIMACHE WILDSOR',
                'specialty' => 'CARDIOLOGIA',
                'schedules' => [
                    ['available_from' => '11:00', 'available_to' => '12:30', 'days' => 'LUNES A VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'HONORIO Q. CARMEN',
                'specialty' => 'CARDIOLOGIA',
                'schedules' => [
                    ['available_from' => '16:00', 'available_to' => '18:00', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'HURTADO AVILES MARCO',
                'specialty' => 'CIRUGIA DE CABEZA Y CUELLO',
                'schedules' => [
                    ['available_from' => '10:00', 'available_to' => '12:00', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'IQUISE ILACIO VICTOR',
                'specialty' => 'CIRUGIA CARDIOVASCULAR',
                'schedules' => [
                    ['available_from' => '14:30', 'available_to' => '15:30', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 250],
                ]
            ],
            [
                'name' => 'GUTIERREZ JUAN CARLOS',
                'specialty' => 'CIRUGIA VASCULAR',
                'schedules' => [
                    ['available_from' => '15:00', 'available_to' => '16:00', 'days' => 'A LLAMADO', 'price' => 200],
                ]
            ],
            [
                'name' => 'COLQUE MARDÍN ALEX',
                'specialty' => 'COLOPROCTOLOGIA',
                'schedules' => [
                    ['available_from' => '08:00', 'available_to' => '09:00', 'days' => 'MARTES A VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'ARANIBAR BENITO PAOLA',
                'specialty' => 'DERMATOLOGIA',
                'schedules' => [
                    ['available_from' => '16:30', 'available_to' => '18:30', 'days' => 'LUNES A VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'ESCALERA PAQUI JORGE',
                'specialty' => 'ENDOCRINOLOGIA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '11:00', 'days' => 'MARTES, JUEVES', 'price' => 200],
                    ['available_from' => '20:30', 'available_to' => '21:30', 'days' => 'LUNES, MIERCOLES', 'price' => 200],
                    ['available_from' => '14:00', 'available_to' => '15:00', 'days' => 'VIERNES', 'price' => 200],
                ]
            ],

//            GASTROENTEROLOGIA	LLANQUE MAMANI RODRIGO	10:00	12:00	LUNES A VIERNES	200 BS.-
//        VILLEGAS YAÑEZ JOSE LUIS	16:30	19:30	LUNES A VIERNES
//GERIATRIA	MENDOZA MORA BRESNEV	14:00	15:00	LUNES Y JUEVES	200 BS.-
//        HEMATOLOGIA	MENESES HIDALGO ERIC	12:00	14:00	MARTES Y JUEVES	200 BS.-
//        17:00	19:00	LUNES, MIERCOLES Y VIERNES
//NEFROLOGIA	CACERES PEREYRA CARLOS	15:00	16:00	LUNES A VIERNES	200 BS.-
//        NEUMOLOGIA	RODRIGUEZ RENJEL MARIA	18:30	-	LUNES A VIERNES	200 BS.-
//        NEUROCIRUGIA	CHECA DAVID	10:00	12:00	LUNES, MARTES Y VIERNES	200 BS.-
//        PRADO NITHZE	17:00	19:00	LUNES A VIERNES
//NEUROLOGIA CLINICA	QUILO IGOR	A LLAMADO		LUNES Y MARTES	250 BS.-
//        POZO MILENNA	17:00	19:00	LUNES A VIERNES
//NUTRICION	MEDRANO KATIA	14:00	15:00	LUNES A VIERNES	150 BS.-
//        ONCOLOGIA	CESPEDES ZENON	16:00	18:00	LUNES A JUEVES	200 BS.-
//        OTORRINOLARINGOLOGÍA	CANAVIRI PATRICIA	14:00	16:00	LUNES Y MIERCOLES	200 BS.-
//        PSICOLOGIA	RAMIREZ ANDREA	16:00	17:00	MARTES, JUEVES Y VIERNES	150 BS.-
//        RODRIGUEZ NADIR	16:00	19:00	LUNES
//PSIQUIATRIA	BUSTOS DAVID	A LLAMADO		A LLAMADO	150 BS.-
//        REUMATOLOGIA	LUCANA LINA	A LLAMADO		LUNES A VIERNES	200 BS.-
//        MAMANI MARIANA	09:00	10:30	MIERCOLES
//		11:00	13:00	MARTES Y JUEVES
//UROLOGIA	CALDERON ALBERTO	11:30	12:30	MARTES, MIERCOLES Y VIERNES	200 BS.-
//        19:00	-	JUEVES
//SUB-ESPECIALIDADES MEDICAS
//CARDIOLOGIA PEDIATRICA	SANCHEZ ROSALIA	17:00	19:00	LUNES, MIÉRCOLES Y VIERNES	200 BS.-
//        ENDOCRINOLOGIA PEDIATRICA	MAMANI ROLY	A LLAMADO		A LLAMADO	200 BS.-
//        NEUROLOGIA PEDIATRICA	HERRERA VERONICA	09:00	11:00	SABADOS	200 BS.-
//        NEONATOLOGIA Y UTI NEONATAL	HERRERA LILY	A REQUERIMIENTO		A REQUERIMIENTO
//	TUPA REYNALDO
//TRAUMATOLOGIA PEDIATRICA	TAPIA GUIDO	15:00	17:00	LUNES, MIERCOLES Y VIERNES	200 BS.-
//        UTI - PEDIATRICA	VERA BETZI	A REQUERIMIENTO		A REQUERIMIENTO
//SI LAS PERSONAS DESEAN AGENDAR EN OTRO HORARIO FUERA DE LOS REGISTRADOS SI O SI DEBEN ESPERAR A QUE EL AGENTE SE COMUNIQUE CON ELLOS PORQUE NORMALMENTE NO ATIENDEN EN OTROS HORARIOS.
//        OTROS SERVICIOS
//MEDICINA GENERAL		24 HRS.		TODOS LOS DIAS	80 BS
//CONSULTA EMERGENCIAS		24 HRS.		TODOS LOS DIAS	100 BS.-
//        RAYOS X		24 HRS.		TODOS LOS DIAS	EL PRECIO VARIA SEGÚN CADA ESTUDIO, UNA PERSONA DEBERÁ CONTACTARSE CON EL INTERESADO
//TOMOGRAFÍA		24 HRS.		TODOS LOS DIAS	EL PRECIO VARIA SEGÚN CADA ESTUDIO, UNA PERSONA DEBERÁ CONTACTARSE CON EL INTERESADO
//LABORATORIO		24 HRS.		TODOS LOS DIAS	EL PRECIO VARIA SEGÚN CADA ESTUDIO, UNA PERSONA DEBERÁ CONTACTARSE CON EL INTERESADO
//ECOGRAFÍA		24 HRS.		TODOS LOS DIAS	EL PRECIO VARIA SEGÚN CADA ESTUDIO, UNA PERSONA DEBERÁ CONTACTARSE CON EL INTERESADO
//HORARIOS DE VISITA A PACIENTES INTERNADOS		09:00	13:00	LUNES A VIERNES
//		15:00	20:00
//		09:00	20:00	SABADO, DOMINGO, FERIADOS
//FISIOTERAPIA		08:00	20:00	LUNES A VIERNES	50 BS.-
//        09:00	13:00	SABADOS
//ELECTROCARDIOGRAMA	ARANDIA LIMACHE WILDSOR	11:00	12:30	LUNES A VIERNES	300 BS.-
//        HONORIO Q. CARMEN	16:00	18:00	LUNES, MIERCOLES Y VIERNES
//VALORACION PREQUIRURGICA	ARANDIA LIMACHE WILDSOR	11:00	12:30	LUNES A VIERNES	500 BS.-
//        HONORIO Q. CARMEN	16:00	18:00	LUNES, MIERCOLES Y VIERNES
//CAMPAÑA GINECOLOGÍA Y OBSTETRICIA
//GINECOLOGIA	ARELLANO ALANEZ MIGUEL	09:00	11:00	LUNES A VIERNES	150 BS.-
//        MAMANI MAMANI VIDAL	11:00	13:00	LUNES A VIERNES
//	CONDARCO HUALLARTE KENIA	15:00	18:00	LUNES A VIERNES
//PAQUETES DE CAMPAÑA:
//CESAREA INCLUYE:	HONORARIOS (GINECOLOGO, PEDIATRA, ANESTESIOLOGO, AYUDANTE), INTERNACIÓN EN SALA VIP, MEDICAMENTOS, LABORATORIOS				4600 BS.-
//        PARTO INCLUYE:	HONORARIOS (GINECOLOGO, PEDIATRA), INTERNACIÓN EN SALA VIP, MEDICAMENTOS, LABORATORIOS				3000 BS.-
//        INFORMACIÓN CUANDO PREGUNTAN:	La campaña será hasta el 2 de julio de este año si desea agendar las cirugias y/o consultas se deberá contactar con agente para que agenden. Si la cesarea/parto desea programar para otro mes esto si es posible mediante la programación con el ginecologo


            [
                'name' => 'MAMANI MARIANA',
                'specialty' => 'REUMATOLOGIA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '10:30', 'days' => 'MIERCOLES', 'price' => 200],
                    ['available_from' => '11:00', 'available_to' => '13:00', 'days' => 'MARTES Y JUEVES', 'price' => 200],
                ]
            ],
            [
                'name' => 'CALDERON ALBERTO',
                'specialty' => 'UROLOGIA',
                'schedules' => [
                    ['available_from' => '11:30', 'available_to' => '12:30', 'days' => 'MARTES, MIERCOLES Y VIERNES', 'price' => 200],
                    ['available_from' => '19:00', 'available_to' => '21:00', 'days' => 'JUEVES', 'price' => 200],
                ]
            ],
            [
                'name' => 'SANCHEZ ROSALIA',
                'specialty' => 'CARDIOLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => '17:00', 'available_to' => '19:00', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'MAMANI ROLY',
                'specialty' => 'ENDOCRINOLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => 'A LLAMADO', 'available_to' => null, 'days' => 'A LLAMADO', 'price' => 200],
                ]
            ],
            [
                'name' => 'HERRERA VERONICA',
                'specialty' => 'NEUROLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '11:00', 'days' => 'SABADOS', 'price' => 200],
                ]
            ],
            [
                'name' => 'HERRERA LILY',
                'specialty' => 'NEONATOLOGIA Y UTI NEONATAL',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => null],
                ]
            ],
            [
                'name' => 'TAPIA GUIDO',
                'specialty' => 'TRAUMATOLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => '15:00', 'available_to' => '17:00', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'VERA BETZI',
                'specialty' => 'UTI - PEDIATRICA',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => null],
                ]
            ],
            [
                'name' => 'LLANQUE MAMANI RODRIGO',
                'specialty' => 'GASTROENTEROLOGIA',
                'schedules' => [
                    ['available_from' => '10:00', 'available_to' => '12:00', 'days' => 'LUNES A VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'VILLEGAS YAÑEZ JOSE LUIS',
                'specialty' => 'GASTROENTEROLOGIA',
                'schedules' => [
                    ['available_from' => '16:30', 'available_to' => '19:30', 'days' => 'LUNES A VIERNES', 'price' => null],
                ]
            ],
            [
                'name' => 'MENDOZA MORA BRESNEV',
                'specialty' => 'GERIATRIA',
                'schedules' => [
                    ['available_from' => '14:00', 'available_to' => '15:00', 'days' => 'LUNES Y JUEVES', 'price' => 200],
                ]
            ],
            [
                'name' => 'MENESES HIDALGO ERIC',
                'specialty' => 'HEMATOLOGIA',
                'schedules' => [
                    ['available_from' => '12:00', 'available_to' => '14:00', 'days' => 'MARTES Y JUEVES', 'price' => 200],
                    ['available_from' => '17:00', 'available_to' => '19:00', 'days' => null, 'price' => null],
                ]
            ],
            [
                'name' => "CACERES PEREYRA CARLOS",
                "specialty" => "NEFROLOGIA",
                "schedules" => [
                    ["available_from" => "15:00", "available_to" => "16:00", "days" => "LUNES A VIERNES", "price" => 200],
                ]
            ],
            [
                "name" => "RODRIGUEZ RENJEL MARIA",
                "specialty" => "NEUMOLOGIA",
                "schedules" => [
                    ["available_from" => "18:30", "available_to" => null, "days" => "LUNES A VIERNES", "price" => 200],
                ]
            ],
            [
                'name' => 'CHECA DAVID',
                'specialty' => 'NEUROCIRUGIA',
                'schedules' => [
                    ['available_from' => '10:00', 'available_to' => '12:00', 'days' => 'LUNES, MARTES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'PRADO NITHZE',
                'specialty' => 'NEUROCIRUGIA',
                'schedules' => [
                    ['available_from' => '17:00', 'available_to' => '19:00', 'days' => 'LUNES A VIERNES', 'price' => null],
                ]
            ],
            [
                'name' => 'QUILO IGOR',
                'specialty' => 'NEUROLOGIA CLINICA',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => 250],
                ]
            ],
            [
                'name' => 'POZO MILENNA',
                'specialty' => 'POZO MILENNA',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => null],
                ]
            ],
            [
                "name" => "MEDRANO KATIA",
                "specialty" => "NUTRICION",
                "schedules" => [
                    ["available_from" => "14:00", "available_to" => "15:00", "days" => "LUNES A VIERNES", "price" => 150],
                ]
            ],
            [
                "name" => "CESPEDES ZENON",
                "specialty" => "ONCOLOGIA",
                "schedules" => [
                    ["available_from" => "16:00", "available_to" => "18:00", "days" => "LUNES A JUEVES", "price" => 200],
                ]
            ],
            [
                "name" => "CANAVIRI PATRICIA",
                "specialty" => "OTORRINOLARINGOLOGÍA",
                "schedules" => [
                    ["available_from" => "14:00", "available_to" => "16:00", "days" => "LUNES Y MIERCOLES", "price" => 200],
                ]
            ],
            [
                'name' => 'RAMIREZ ANDREA',
                'specialty' => 'PSICOLOGIA',
                'schedules' => [
                    ['available_from' => '16:00', 'available_to' => '17:00', 'days' => 'MARTES, JUEVES Y VIERNES', 'price' => 150],
                ]
            ],
            [
                'name' => 'RODRIGUEZ NADIR',
                'specialty' => 'RODRIGUEZ NADIR',
                'schedules' => [
                    ['available_from' => '16:00', 'available_to' => '19:00', 'days' => 'LUNES', 'price' => null],
                ]
            ],
            [
                "name" => "BUSTOS DAVID",
                "specialty" => "PSIQUIATRIA",
                "schedules" => [
                    ["available_from" => null, "available_to" => null, "days" => null, "price" => 150],
                ]
            ],
            [
                "name" => "LUCANA LINA",
                "specialty" => "REUMATOLOGIA",
                "schedules" => [
                    ["available_from" => null, "available_to" => null, "days" => null, "price" => 200],
                ]
            ],
            [
                "name" => "MAMANI MARIANA",
                "specialty" => "MAMANI MARIANA",
                "schedules" => [
                    ["available_from" => "09:00", "available_to" => "10:30", "days" => "MIERCOLES", "price" => 200],
                    ["available_from" => "11:00", "available_to" => "13:00", "days" => "MARTES Y JUEVES", "price" => 200],
                ]
            ],
            [
                "name" => "CALDERON ALBERTO",
                "specialty" => "UROLOGIA",
                "schedules" => [
                    ["available_from" => "11:30", "available_to" => "12:30", "days" => "MARTES, MIERCOLES Y VIERNES", "price" => 200],
                    ["available_from" => "19:00", "available_to" => "21:00", "days" => "JUEVES", "price" => 200],
                ]
            ],
            [
                'name' => 'SANCHEZ ROSALIA',
                'specialty' => 'CARDIOLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => '17:00', 'available_to' => '19:00', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'MAMANI ROLY',
                'specialty' => 'ENDOCRINOLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => 200],
                ]
            ],
            [
                'name' => 'HERRERA VERONICA',
                'specialty' => 'NEUROLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => '09:00', 'available_to' => '11:00', 'days' => 'SABADOS', 'price' => 200],
                ]
            ],
            [
                'name' => 'HERRERA LILY',
                'specialty' => 'NEONATOLOGIA Y UTI NEONATAL',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => null],
                ]
            ],
            [
                'name' => 'TAPIA GUIDO',
                'specialty' => 'TRAUMATOLOGIA PEDIATRICA',
                'schedules' => [
                    ['available_from' => '15:00', 'available_to' => '17:00', 'days' => 'LUNES, MIERCOLES Y VIERNES', 'price' => 200],
                ]
            ],
            [
                'name' => 'VERA BETZI',
                'specialty' => 'UTI - PEDIATRICA',
                'schedules' => [
                    ['available_from' => null, 'available_to' => null, 'days' => null, 'price' => null],
                ]
            ],
        ];

        foreach ($doctores as $doc) {
            $doctor = Doctor::create([
                'name' => $doc['name'],
                'specialty' => $doc['specialty'],
                'phone' => null,
            ]);

            foreach ($doc['schedules'] as $schedule) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'available_from' => $schedule['available_from'],
                    'available_to' => $schedule['available_to'],
                    'days' => $schedule['days'],
                    'price' => $schedule['price'],
                ]);
            }
        }
    }
}
