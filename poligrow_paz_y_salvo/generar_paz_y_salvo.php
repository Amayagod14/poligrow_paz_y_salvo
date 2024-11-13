<?php
// Configuración de la base de datos
require_once 'includes/database.php';

// Clase para manejar el PDF
require('fpdf/fpdf.php');

class PazYSalvoPDF extends FPDF {
    public function __construct() {
        parent::__construct();
        // Establecer la codificación para caracteres especiales
        $this->SetFont('Arial', '', 12);
        $this->AddPage();
        $this->SetAuthor('Poligrow Colombia');
    }

    public function Header() {
        // Configurar márgenes
        $this->SetMargins(20, 20, 20);
        
        // Logo - Ajustado a la izquierda
        $this->Image('img/logo.jpg', 20, 15, 40);
        
        // Mover a la posición del título (alineado con el logo)
        $this->SetXY(70, 20);
        
        // Título alineado con el logo
        $this->SetFont('Arial', 'B', 14);
        // Convertir a UTF-8 y luego a ISO-8859-1 para manejar tildes
        $titulo = utf8_decode('PAZ Y SALVO - TERMINACIÓN DE CONTRATO');
        $this->Cell(0, 8, $titulo, 0, 1, 'L');
        
        // Subtítulo
        $this->SetXY(70, 30);
        $this->SetFont('Arial', '', 11);
        $subtitulo = utf8_decode('POLIGROW COLOMBIA S.A.S.');
        $this->Cell(0, 8, $subtitulo, 0, 1, 'L');
        
        // Espacio después del encabezado
        $this->Ln(30);
    }
    
    public function Footer() {
        $this->SetY(-35);
        $this->SetFont('Arial', '', 8);
        // Convertir todos los textos del pie de página
        $this->Cell(0, 4, utf8_decode('Poligrow Colombia S.A.S. - NIT 900.335.180-3'), 0, 1, 'C');
        $this->Cell(0, 4, utf8_decode('Calle 97 Bis No. 19-20 Oficina 702'), 0, 1, 'C');
        $this->Cell(0, 4, utf8_decode('Bogotá D.C. - Colombia'), 0, 1, 'C');
        $this->Cell(0, 4, utf8_decode('Teléfono: +57 601 7438480'), 0, 1, 'C');
        $this->Cell(0, 4, 'www.poligrow.com', 0, 1, 'C');
    }
}

// Clase para manejar el Paz y Salvo
class PazYSalvo {
    private $conn;
    private $departments = [
        'ALMACEN-IDEMA',
        'SGSST',
        'DOTACION',
        'CONTABILIDAD',
        'COORDINADOR/DIRECTOR',
        'ALIMENTACION',
        'HOSPEDAJE/HERRAMIENTA',
        'FUNDACION',
        'FONDO DE EMPLEADOS',
        'SISTEMAS'
    ];
    
    public function __construct() {
        $this->conn = DatabaseConfig::getConnection();
    }
    
    public function procesarFormulario() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificar si se está editando un Paz y Salvo existente
            if (isset($_POST['empleado_id']) && !empty($_POST['empleado_id'])) {
                $empleado_id = $_POST['empleado_id'];
            } else {
                $empleado_id = $this->guardarEmpleado();
            }

            $this->guardarFirmas($empleado_id);
            $this->actualizarEstadoPazYSalvo($empleado_id);

            // Si se presionó el botón "Guardar información", no generar el PDF
            if (isset($_POST['guardar_y_salir'])) {
                // Redirigir al usuario al dashboard 
                header('Location: dashboard.php');
                exit;
            }

            // Validar campos de firma solo si se presionó "Generar Paz y Salvo"
            if (isset($_POST['generate_pdf'])) {
                // Validar que todos los campos de firma estén llenos
                foreach ($this->departments as $index => $dept) {
                    if (empty($_FILES["firma_dept_$index"]['tmp_name']) || 
                        empty($_POST["nombre_firmante_$index"]) || 
                        empty($_POST["fecha_firma_$index"])) {
                        // Mostrar un mensaje de error 
                        echo "Error: Todos los campos de firma son obligatorios para generar el Paz y Salvo.";
                        return false;
                    }
                }

                // Generar el PDF si el estado es 'completado'
                if ($this->obtenerEstadoPazYSalvo($empleado_id) === 'completado') {
                    $this->generarPDF($empleado_id);
                }
            }
            
            return true;
        }
        return false; // Retornar false si no se envió el formulario
    }
    
    private function guardarEmpleado() {
        $stmt = $this->conn->prepare("
            INSERT INTO empleados (
                nombre, 
                documento, 
                cargo,
                area, 
                fecha_ingreso, 
                fecha_retiro, 
                motivo_retiro,
                estado 
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $fecha_ingreso = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
        $fecha_retiro = date('Y-m-d', strtotime($_POST['fecha_retiro']));
        $estado = 'pendiente'; // Estado inicial del Paz y Salvo
        
// Obtener los valores de $_POST
$valores = [
    $_POST['nombre'],
    $_POST['documento'],
    $_POST['cargo'],
    $_POST['area'],
    $fecha_ingreso,
    $fecha_retiro,
    $_POST['motivo_retiro'],
    $estado
];

// Construir la cadena de tipos dinámicamente
$tipos = str_repeat('s', count($valores));

// Llamar a bind_param() con la cadena de tipos dinámica y los valores
$stmt->bind_param($tipos, ...$valores);

$stmt->execute();
return $this->conn->insert_id;
    }


    
    private function guardarFirmas($empleado_id) {
        $stmt = $this->conn->prepare("
            INSERT INTO firmas_departamento (
                paz_y_salvo_id,
                departamento,
                nombre_firmante,
                fecha_firma,
                imagen_firma
            ) VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($this->departments as $index => $dept) {
            if (isset($_FILES["firma_dept_$index"]) && 
                $_FILES["firma_dept_$index"]['error'] === 0 &&
                !empty($_POST["nombre_firmante_$index"]) &&
                !empty($_POST["fecha_firma_$index"])) {

                $firma = file_get_contents($_FILES["firma_dept_$index"]['tmp_name']);
                $fecha = date('Y-m-d', strtotime($_POST["fecha_firma_$index"]));

                // Verificar si ya existe una firma para este departamento y empleado
                $stmt_check = $this->conn->prepare("SELECT id FROM firmas_departamento WHERE paz_y_salvo_id = ? AND departamento = ?");
                $stmt_check->bind_param("is", $empleado_id, $dept);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    // Actualizar la firma existente
                    $stmt_check->bind_result($firma_id);
                    $stmt_check->fetch();
                    $stmt_update = $this->conn->prepare("UPDATE firmas_departamento SET nombre_firmante = ?, fecha_firma = ?, imagen_firma = ? WHERE id = ?");
                    $stmt_update->bind_param("sssi", $_POST["nombre_firmante_$index"], $fecha, $firma, $firma_id);
                    $stmt_update->execute();
                } else {
                    // Insertar una nueva firma
                    $stmt->bind_param("issss", $empleado_id, $dept, $_POST["nombre_firmante_$index"], $fecha, $firma);
                    $stmt->execute();
                }

                $stmt_check->close();
            }
        }
    }

    private function actualizarEstadoPazYSalvo($empleado_id) {
        // Contar las firmas existentes para el empleado
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM firmas_departamento WHERE paz_y_salvo_id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $stmt->bind_result($num_firmas);
        $stmt->fetch();
        $stmt->close();

        // Determinar el estado del Paz y Salvo
        if ($num_firmas == 0) {
            $estado = 'pendiente';
        } elseif ($num_firmas == count($this->departments)) {
            $estado = 'completado';
        } else {
            $estado = 'en_proceso';
        }

        // Actualizar el estado en la tabla empleados
        $stmt = $this->conn->prepare("UPDATE empleados SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $empleado_id);
        $stmt->execute();
        $stmt->close();
    }

    private function obtenerEstadoPazYSalvo($empleado_id) {
        $stmt = $this->conn->prepare("SELECT estado FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $stmt->bind_result($estado);
        $stmt->fetch();
        $stmt->close();
        return $estado;
    }
    
    private function generarPDF($empleado_id) {
        $pdf = new PazYSalvoPDF();
        
        // Información del empleado con fuente más pequeña
        $pdf->SetFont('Arial', '', 11);
        $pdf->MultiCell(0, 7, $this->getEmpleadoInfo($empleado_id), 0, 'J');
        
        // Agregar firmas con nuevo diseño optimizado
        $this->agregarFirmasDepartamentos($pdf, $empleado_id);
        
        // Generar archivo
        $pdf->Output('D', 'paz_y_salvo_' . date('Y-m-d') . '.pdf');
        exit;
    }
    
    private function getEmpleadoInfo($empleado_id) {
        $stmt = $this->conn->prepare("SELECT * FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $empleado = $result->fetch_assoc();
        $stmt->close();

        return utf8_decode(
            sprintf(
                "Poligrow %s identificado(a) con cedula de ciudadania N. %s " .
                "el cargo %s Colombia SAS certifica que el(la) señor(a) " .
                "desempeña del area de %s quien se encuentra paz y salvo " .
                "con la empresa por concepto de %s",
                $empleado['nombre'],
                $empleado['documento'],
                $empleado['cargo'],
                $empleado['area'],
                $empleado['motivo_retiro']
            )
        );
    }

    private function agregarFirmasDepartamentos($pdf, $empleado_id) {
        $pdf->Ln(10);
        
        // Ajuste de dimensiones para mejor distribución
        $col_width = 85;
        $row_height = 40;// Aumentado para más espacio
        $x_start = 20;
        $x_spacing = 90;
        $y_start = $pdf->GetY();
        $y_spacing = 42;// Aumentado para dar más espacio entre filas
        
        $items_per_page = 6;
        $current_page = 1;

        // Obtener las firmas ya guardadas para el empleado
        $stmt = $this->conn->prepare("SELECT * FROM firmas_departamento WHERE paz_y_salvo_id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $firmas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($this->departments as $index => $dept) {
            // Cambiar de página después de 6 items
            if ($index == $items_per_page) {
                $pdf->AddPage();
                $y_start = $pdf->GetY();
                $current_page = 2;
            }
            
            // Calcular posición
            $col = ($index % 2);
            $row = floor(($index % $items_per_page) / 2);
            
            $x_pos = $x_start + ($col * $x_spacing);
            $y_pos = $y_start + ($row * $y_spacing);
            
            if ($current_page == 2) {
                $y_pos = $y_start + (($row) * $y_spacing);
            }
            
            $pdf->SetXY($x_pos, $y_pos);
            
            // Título del departamento
            $pdf->SetFont('Arial', 'B', 9); // Aumentado tamaño del título
            $pdf->Cell($col_width, 6, $dept, 1, 2, 'C');

            // Buscar la firma correspondiente al departamento
            $firma_encontrada = false;
            foreach ($firmas as $firma) {
                if ($firma['departamento'] === $dept) {
                    $firma_encontrada = true;
                    $imagen_firma = $firma['imagen_firma'];
                    $nombre_firmante = $firma['nombre_firmante'];
                    $fecha_firma = $firma['fecha_firma'];
                    break;
                }
            }

            if ($firma_encontrada) {
                $temp_filename = tempnam(sys_get_temp_dir(), 'firma_') . '.png';
                file_put_contents($temp_filename, $imagen_firma);

                // Calcular posición centrada para la firma
                $firma_width = 35; // Ancho de la firma
                $firma_height = 25; // Alto de la firma
                $x_firma = $x_pos + ($col_width - $firma_width) / 2;
                $y_firma = $y_pos + 8; // Ajustado para centrar verticalmente
                
                // Agregar al PDF con tamaño y posición ajustados
                $pdf->Image(
                    $temp_filename,
                    $x_firma,
                    $y_firma,
                    $firma_width,
                    $firma_height
                );
                
                unlink($temp_filename);
                
                // Nombre y fecha con tamaño de fuente aumentado
                $pdf->SetXY($x_pos, $y_pos + $row_height - 8);
                $pdf->SetFont('Arial', '', 8.5); // Aumentado tamaño de la fuente
                $nombre_firmante = substr($nombre_firmante, 0, 25);
                
                // Ajustar el ancho de las celdas para el nombre y la fecha
                $nombre_width = $col_width * 0.6; // 60% del ancho para el nombre
                $fecha_width = $col_width * 0.4; // 40% del ancho para la fecha
                
                $pdf->Cell($nombre_width, 4, $nombre_firmante, 0, 0, 'L');
                $pdf->Cell($fecha_width, 4, $fecha_firma, 0, 0, 'R');
            }
        }
    }
    
    public function getDepartments() {
        return $this->departments;
    }
}
// Obtener el ID del empleado (si se proporciona en la URL)
$empleado_id = isset($_GET['empleado_id']) ? $_GET['empleado_id'] : null;

// Cargar la información del empleado si se está editando
if ($empleado_id) {
    $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empleado = $result->fetch_assoc();
    $stmt->close();

    // Cargar las firmas
    $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM firmas_departamento WHERE paz_y_salvo_id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $firmas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Inicializar y ejecutar
$pazYSalvo = new PazYSalvo();
if ($pazYSalvo->procesarFormulario()) {
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Paz y Salvo - Poligrow Colombia</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
</head>
<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="flex items-center justify-between mb-8">
        <img src="assets/images/logo.png" alt="Poligrow Logo" class="h-16">
        <h1 class="text-2xl font-bold text-center text-gray-800">
          Paz y Salvo - Terminación de Contrato
        </h1>
      </div>

      <?php
      // Obtener el ID del empleado (si se proporciona en la URL)
      $empleado_id = isset($_GET['empleado_id']) ? $_GET['empleado_id'] : null;

      // Cargar la información del empleado si se está editando
      if ($empleado_id) {
        $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM empleados WHERE id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $empleado = $result->fetch_assoc();
        $stmt->close();

        // Cargar las firmas
        $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM firmas_departamento WHERE paz_y_salvo_id = ?");
        $stmt->bind_param("i", $empleado_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $firmas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
      }
      ?>

      <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="empleado_id" id="empleado_id" value="<?php echo $empleado_id; ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
            <input type="text" name="nombre" required
                   value="<?php echo isset($empleado['nombre']) ? $empleado['nombre'] : ''; ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Documento de identidad</label>
            <input type="text" name="documento" required
                   value="<?php echo isset($empleado['documento']) ? $empleado['documento'] : ''; ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Cargo</label>
            <input type="text" name="cargo" required
                   value="<?php echo isset($empleado['cargo']) ? $empleado['cargo'] : ''; ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Área</label>
            <input type="text" name="area" required
                   value="<?php echo isset($empleado['area']) ? $empleado['area'] : ''; ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Motivo de retiro</label>
            <select name="motivo_retiro" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
              <option value="RENUNCIA" <?php echo (isset($empleado['motivo_retiro']) && $empleado['motivo_retiro'] == 'RENUNCIA') ? 'selected' : ''; ?>>Renuncia</option>
              <option value="TERMINACION DE CONTRATO" <?php echo (isset($empleado['motivo_retiro']) && $empleado['motivo_retiro'] == 'TERMINACION DE CONTRATO') ? 'selected' : ''; ?>>Terminación de contrato</option>
              <option value="MUTUO ACUERDO" <?php echo (isset($empleado['motivo_retiro']) && $empleado['motivo_retiro'] == 'MUTUO ACUERDO') ? 'selected' : ''; ?>>Mutuo acuerdo</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Fecha de ingreso</label>
            <input type="text" name="fecha_ingreso" required class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   value="<?php echo isset($empleado['fecha_ingreso']) ? date('d/m/Y', strtotime($empleado['fecha_ingreso'])) : ''; ?>">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Fecha de retiro</label>
            <input type="text" name="fecha_retiro" required class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   value="<?php echo isset($empleado['fecha_retiro']) ? date('d/m/Y', strtotime($empleado['fecha_retiro'])) : ''; ?>">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
          <?php foreach ($pazYSalvo->getDepartments() as $index => $dept): ?>
            <?php
            // Obtener la firma correspondiente al departamento
            $firma = null;
            if (isset($firmas)) {
              foreach ($firmas as $f) {
                if ($f['departamento'] === $dept) {
                  $firma = $f;
                  break;
                }
              }
            }
            ?>
            <div class="border rounded-lg p-4">
              <h3 class="font-medium text-lg mb-4"><?php echo htmlspecialchars($dept); ?></h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Firma</label>
                  <input type="file" name="firma_dept_<?php echo $index; ?>" accept="image/*"
                         class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                  <?php if ($firma && $firma['imagen_firma']): ?>
                    <img src="data:image/png;base64,<?php echo base64_encode($firma['imagen_firma']); ?>" class="mt-2 h-20 object-contain">
                  <?php endif; ?>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Nombre del firmante</label>
                  <input type="text" name="nombre_firmante_<?php echo $index; ?>"
                         value="<?php echo isset($firma['nombre_firmante']) ? $firma['nombre_firmante'] : ''; ?>"
                         class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                <label class="block text-sm font-medium text-gray-700">Fecha de firma</label>
                <input type="text" name="fecha_firma_<?php echo $index; ?>"
                        value="<?php echo isset($firma['fecha_firma']) ? date('d/m/Y', strtotime($firma['fecha_firma'])) : ''; ?>"
                        class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                </div>
                </div>
                <?php endforeach; ?>
                </div>

                <div class="mt-8 flex justify-center space-x-4">
                <button type="submit" name="guardar_y_salir"
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Guardar información
                </button>
                <button type="submit" name="generate_pdf"
                        class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Generar Paz y Salvo
                </button>
                </div>
                </form>
                </div>

        <footer class="mt-8 text-center text-sm text-gray-600">
            <p>Poligrow Colombia S.A.S. - NIT 900.335.180-3</p>
            <p>Calle 97 Bis No. 19-20 Oficina 702, Bogotá D.C. - Colombia</p>
            <p>Teléfono: +57 601 7438480</p>
        </footer>
    </div>

    <script>
$(document).ready(function() {
    // Configuración del datepicker en español
    $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '< Ant',
        nextText: 'Sig >',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                     'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                          'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);

    // Inicializar datepickers
    $('.datepicker').datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '2000:2030'
    });

    // Validación del formulario
    $('form').on('submit', function(e) {
        let isValid = true;
        const requiredFields = $(this).find('[required]');
        
        // Eliminar el atributo 'required' de los campos de firma al hacer clic en "Guardar información"
        $('button[name="guardar_y_salir"]').on('click', function() {
            $('input[type="file"], input[name^="nombre_firmante_"], input[name^="fecha_firma_"]').removeAttr('required');
        });

        // Volver a agregar el atributo 'required' al hacer clic en "Generar Paz y Salvo"
        $('button[name="generate_pdf"]').on('click', function() {
            $('input[type="file"], input[name^="nombre_firmante_"], input[name^="fecha_firma_"]').attr('required', 'required');
        });

        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('border-red-500');
                
                // Mostrar mensaje de error
                const errorMessage = $('<p class="text-red-500 text-sm mt-1">Este campo es requerido</p>');
                if (!$(this).next('.text-red-500').length) {
                    $(this).after(errorMessage);
                }
            } else {
                $(this).removeClass('border-red-500');
                $(this).next('.text-red-500').remove();
            }
        });

    // Validación de archivos (solo si se presiona "Generar Paz y Salvo")
    if ($('button[name="generate_pdf"]').is(':clicked')) {
            const fileInputs = $('input[type="file"]');
            fileInputs.each(function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSize = file.size / 1024 / 1024; // en MB
                    
                    if (fileSize > 2) {
                        isValid = false;
                        alert('El archivo ' + file.name + ' excede el tamaño máximo permitido de 2MB');
                    }
                    
                    if (!file.type.startsWith('image/')) {
                        isValid = false;
                        alert('El archivo ' + file.name + ' debe ser una imagen');
                    }
                }
            });
        }

        // Guardar la información del empleado mediante AJAX si es un nuevo Paz y Salvo
        if (isValid && $(this).find('input[name="empleado_id"]').val() === "") {
            e.preventDefault(); // Evitar el envío normal del formulario

            // Obtener los datos del formulario
            var formData = new FormData(this);

            $.ajax({
                url: 'guardar_empleado.php',
                type: 'POST',
                data: formData,
                processData: false, // Evitar que jQuery procese los datos
                contentType: false, // Evitar que jQuery establezca el tipo de contenido
                success: function(response) {
                    // Asignar el ID del empleado al campo oculto
                    $('#empleado_id').val(response);

                    // Volver a enviar el formulario con el ID del empleado
                    $('form').submit();
                },
                error: function(xhr, status, error) {
                    console.error(error); // Mostrar el error en la consola
                    alert("Error al guardar la información del empleado.");
                }
            });
        } else if (!isValid) {
            e.preventDefault();
        }
    });

    // Previsualización de firmas
    $('input[type="file"]').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = $('<img>', {
                class: 'mt-2 h-20 object-contain'
            });
            
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
            };
            
            reader.readAsDataURL(file);
            
            // Remover preview anterior si existe
            $(this).next('img').remove();
            $(this).after(preview);
        }
    });
});
</script>
</body>
</html>
