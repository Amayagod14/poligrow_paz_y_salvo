<?php
// Configuración de la base de datos
require_once 'includes/database.php';

require_once 'generar_paz_y_salvo.php';
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
            $this->actualizarEmpleado($empleado_id); 
            $paz_y_salvo_id = $this->obtenerPazYSalvoId($empleado_id);
        } else {
            // Validar si ya existe un Paz y Salvo con el mismo documento
            if ($this->existePazYSalvo($_POST['documento'])) {
                echo "Error: Ya existe un Paz y Salvo para el empleado con este documento de identidad.";
                return false;
            }
            $empleado_id = $this->guardarEmpleado();
            $paz_y_salvo_id = $this->guardarPazYSalvo($empleado_id);
        }

        $this->guardarFirmas($paz_y_salvo_id); // Pasar el ID del Paz y Salvo
        $this->actualizarEstadoPazYSalvo($paz_y_salvo_id); // Pasar el ID del Paz y Salvo

        // Si se presionó el botón "Guardar información", no generar el PDF
        if (isset($_POST['guardar_y_salir'])) {
            // Redirigir al usuario al dashboard 
            header('Location: dashboard.php');
            exit;
        }

        // Generar el PDF si se presionó "Generar Paz y Salvo" (sin validar campos de firma)
        if (isset($_POST['generate_pdf'])) { 
            $this->generarPDF($empleado_id);
        }
        
        return true;
    }
    return false; // Retornar false si no se envió el formulario
}

  private function guardarPazYSalvo($empleado_id) {
    $stmt = $this->conn->prepare("INSERT INTO paz_y_salvo (empleado_id) VALUES (?)");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    return $stmt->insert_id;
  }

  private function obtenerPazYSalvoId($empleado_id) {
    $stmt = $this->conn->prepare("SELECT id FROM paz_y_salvo WHERE empleado_id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $stmt->bind_result($paz_y_salvo_id);
    $stmt->fetch();
    $stmt->close();
    return $paz_y_salvo_id;
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
            motivo_retiro 
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $fecha_ingreso = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
    $fecha_retiro = date('Y-m-d', strtotime($_POST['fecha_retiro']));

    // Obtener los valores de $_POST (sin el estado)
    $valores = [
      $_POST['nombre'],
      $_POST['documento'],
      $_POST['cargo'],
      $_POST['area'],
      $fecha_ingreso,
      $fecha_retiro,
      $_POST['motivo_retiro']
    ];

    // Construir la cadena de tipos dinámicamente
    $tipos = str_repeat('s', count($valores));

    // Llamar a bind_param() con la cadena de tipos dinámica y los valores
    $stmt->bind_param($tipos, ...$valores);

    $stmt->execute();
    return $this->conn->insert_id;
  }

  private function actualizarEmpleado($empleado_id) {
    $stmt = $this->conn->prepare("
        UPDATE empleados SET 
            nombre = ?, 
            documento = ?, 
            cargo = ?,
            area = ?, 
            fecha_ingreso = ?, 
            fecha_retiro = ?, 
            motivo_retiro = ?
        WHERE id = ?
    ");

    $fecha_ingreso = date('Y-m-d', strtotime($_POST['fecha_ingreso']));
    $fecha_retiro = date('Y-m-d', strtotime($_POST['fecha_retiro']));

    $stmt->bind_param("sssssssi",
      $_POST['nombre'],
      $_POST['documento'],
      $_POST['cargo'],
      $_POST['area'],
      $fecha_ingreso,
      $fecha_retiro,
      $_POST['motivo_retiro'],
      $empleado_id
    );

    $stmt->execute();
  }

  private function existePazYSalvo($documento) {
    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM paz_y_salvo p JOIN empleados e ON p.empleado_id = e.id WHERE e.documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
  }

  private function guardarFirmas($paz_y_salvo_id) { // Actualizar para recibir el ID del Paz y Salvo
    $stmt = $this->conn->prepare("
        INSERT INTO firmas (
            paz_y_salvo_id,
            departamento,
            nombre_firmante,
            fecha_firma,
            imagen_firma
        ) VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nombre_firmante = VALUES(nombre_firmante),
            fecha_firma = VALUES(fecha_firma),
            imagen_firma = VALUES(imagen_firma)
    ");

    foreach ($this->departments as $index => $dept) {
        if (isset($_FILES["firma_dept_$index"]) && 
            $_FILES["firma_dept_$index"]['error'] === 0 &&
            !empty($_POST["nombre_firmante_$index"]) &&
            !empty($_POST["fecha_firma_$index"])) {

            $firma = file_get_contents($_FILES["firma_dept_$index"]['tmp_name']);
            $fecha = date('Y-m-d', strtotime($_POST["fecha_firma_$index"]));

            $stmt->bind_param("issss", 
                $paz_y_salvo_id,
                $dept,
                $_POST["nombre_firmante_$index"],
                $fecha,
                $firma
            );

            $stmt->execute();
        }
        // Si no se envía una nueva firma, pero ya existe una en la base de datos, 
        // mantener la existente
        elseif (!empty($_POST["nombre_firmante_$index"]) &&
            !empty($_POST["fecha_firma_$index"])) {

            // Obtener la firma existente
            $stmt_select = $this->conn->prepare("SELECT imagen_firma FROM firmas WHERE paz_y_salvo_id = ? AND departamento = ?");
            $stmt_select->bind_param("is", $paz_y_salvo_id, $dept);
            $stmt_select->execute();
            $stmt_select->bind_result($imagen_firma_existente);
            $stmt_select->fetch();
            $stmt_select->close();

            // Actualizar la firma (nombre, fecha e imagen si existe)
            $stmt_update = $this->conn->prepare("UPDATE firmas SET nombre_firmante = ?, fecha_firma = ? WHERE paz_y_salvo_id = ? AND departamento = ?");
            $stmt_update->bind_param("sssi", $_POST["nombre_firmante_$index"], $fecha, $paz_y_salvo_id, $dept);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }
}

    private function actualizarEstadoPazYSalvo($paz_y_salvo_id) { // Actualizar para recibir el ID del Paz y Salvo
        // Contar las firmas existentes para el Paz y Salvo
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM firmas WHERE paz_y_salvo_id = ?");
        $stmt->bind_param("i", $paz_y_salvo_id);
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
    
        // Actualizar el estado en la tabla paz_y_salvo (CORREGIDO)
        $stmt = $this->conn->prepare("UPDATE paz_y_salvo SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $estado, $paz_y_salvo_id);
        $stmt->execute();
        $stmt->close();
    }

    private function obtenerEstadoPazYSalvo($empleado_id) {
        $stmt = $this->conn->prepare("SELECT estado FROM paz_y_salvo WHERE id = ?");
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
    
        // Obtener el ID del Paz y Salvo
        $paz_y_salvo_id = $this->obtenerPazYSalvoId($empleado_id);
    
        // Obtener las firmas de la base de datos
        $stmt = $this->conn->prepare("SELECT * FROM firmas WHERE paz_y_salvo_id = ?");
        $stmt->bind_param("i", $paz_y_salvo_id); 
        $stmt->execute();
        $result = $stmt->get_result();
        $firmas = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        // Agregar las firmas al PDF
        $this->agregarFirmasDepartamentos($pdf, $firmas); // Pasar las firmas obtenidas de la base de datos
    
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

    private function agregarFirmasDepartamentos($pdf, $firmas) { // Recibir las firmas como parámetro
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
    $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM firmas WHERE paz_y_salvo_id = ?");
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