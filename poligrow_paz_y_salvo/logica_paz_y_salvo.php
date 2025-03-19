<?php
session_start();
// Configuración de la base de datos
require_once 'includes/database.php';

// Clase para manejar el PDF
require('fpdf/fpdf.php');

class PazYSalvoPDF extends FPDF {
    private $nombreEmpleado; // Nueva propiedad para el nombre del empleado

    public function __construct() {
        parent::__construct();
        $this->SetFont('Arial', '', 12);
        $this->AddPage();
        $this->SetAuthor('Poligrow Colombia');

        // Ajustar márgenes para aprovechar mejor el espacio
        $this->SetMargins(10, 10, 10); // Márgenes izquierdo, superior, derecho
    }

    // Nuevo método para establecer el nombre del empleado
    public function setNombreEmpleado($nombre) {
        $this->nombreEmpleado = $nombre;
    }

    public function Header() {
        // Logo - Ajustado a la izquierda con ancho completo
        $this->Image('img/cabeza.png', 10, 5, 190); 

        // Mover a la posición del título (centrado debajo de la imagen)
        $this->SetXY(20, 30); 

        // Título centrado
        $this->SetFont('Arial', 'B', 12);
        $titulo = utf8_decode('PAZ Y SALVO TERMINACIÓN DE CONTRATO');
        $this->Cell(0, 8, $titulo, 0, 1, 'C');

        // Espacio después del encabezado
        $this->Ln(5); // Reducido el espacio 
    }

    public function Footer() {
        // Configurar la fuente
        $this->SetFont('Arial', '', 12);
        $this->SetY(-75); // Posición a 45mm del final
    
        // Ancho total de la página
        $totalWidth = 190; // Ajusta según el tamaño de la página
    
        // Ancho de cada columna
        $columnWidth = $totalWidth / 2;
    
        // Agregar texto "Firma Gestión Humana:"
        $this->SetX(20); // Alinear con el margen izquierdo
        $this->Cell($columnWidth, 10, utf8_decode('Firma Responsable Nomina:'), 0, 0, 'L'); // Primera columna
    
        // Agregar texto "Firma:"
        $this->SetX(20 + $columnWidth); // Mover a la segunda columna
        $this->Cell($columnWidth, 10, utf8_decode('Firma Empleado:'), 0, 1, 'L'); // Segunda columna
    
        // Agregar el nombre predeterminado en negrita
        $this->SetX(20); // Alinear con la primera columna
        $this->SetFont('Arial', 'B', 12);
        $this->Cell($columnWidth, 10, utf8_decode('CARLOS FRAGOZO'), 0, 0, 'L'); // Nombre en la primera columna
    
        // Agregar el nombre del empleado en negrita
        $this->SetX(20 + $columnWidth); // Mover a la segunda columna
        $this->Cell($columnWidth, 10, utf8_decode($this->nombreEmpleado), 0, 1, 'L'); // Nombre en la segunda columna
    
        // Agregar la imagen de firma predeterminada de Gestión Humana
        $this->SetX(20); // Alinear con la primera columna
        $this->Image('img/firma.png', $this->GetX(), $this->GetY(), 60); // Ajusta la ruta y el tamaño según sea necesario
    
       
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

            // Verificar si el usuario es admin
            if ($_SESSION['es_admin']) {
                // Si es admin, solo guardar las firmas
                $paz_y_salvo_id = $this->obtenerPazYSalvoId($empleado_id);
                $this->guardarFirmas($paz_y_salvo_id);
                $this->actualizarEstadoPazYSalvo($paz_y_salvo_id);
            } else {
                // Si no es admin, actualizar la información del empleado
                $this->actualizarEmpleado($empleado_id);
                $paz_y_salvo_id = $this->obtenerPazYSalvoId($empleado_id);
                $this->guardarFirmas($paz_y_salvo_id);
                $this->actualizarEstadoPazYSalvo($paz_y_salvo_id);
            }
        } else {
            // Si no se está editando, crear un nuevo Paz y Salvo
            // Validar si ya existe un Paz y Salvo con la misma cédula
            if ($this->existePazYSalvo($_POST['cedula'])) { 
                echo "Error: Ya existe un Paz y Salvo para el empleado con esta cédula.";
                return FALSE;
            }
            // Obtener el ID del usuario de la sesión
            $usuario_id = $_SESSION['user_id'];
            $empleado_id = $this->guardarEmpleado($usuario_id); // Pasar el ID del usuario
            $paz_y_salvo_id = $this->guardarPazYSalvo($empleado_id);
            $this->guardarFirmas($paz_y_salvo_id);
            $this->actualizarEstadoPazYSalvo($paz_y_salvo_id);
        }

        // Si se presionó el botón "Guardar información", no generar el PDF
        if (isset($_POST['guardar_y_salir'])) {
            // Redirigir al usuario al dashboard 
            header('Location: admin_dashboard.php');
            exit;
        }

        // Generar el PDF si se presionó "Generar Paz y Salvo"
        if (isset($_POST['generate_pdf'])) {
            $this->generarPDF($empleado_id);
        }

        return TRUE;
    }
    return FALSE; // Retornar false si no se envió el formulario
}



  private function obtenerFirmasPazYSalvo($paz_y_salvo_id) {
    $stmt = $this->conn->prepare("SELECT * FROM firmas WHERE paz_y_salvo_id = ?");
    $stmt->bind_param("i", $paz_y_salvo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $firmas = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $firmas;
  }

 


public function crearPazYSalvo($empleado_id) {
  $stmt = $this->conn->prepare("INSERT INTO paz_y_salvo (empleado_id, estado) VALUES (?, 'pendiente')");
  $stmt->bind_param("i", $empleado_id);
  
  if ($stmt->execute()) {
      // Obtener el ID del Paz y Salvo recién creado
      $paz_y_salvo_id = $stmt->insert_id;

      // Actualizar la información del empleado con los datos del formulario
      $stmt = $this->conn->prepare("
          UPDATE empleados SET 
              fecha_ingreso = ?, 
              fecha_retiro = ?, 
              motivo_retiro = ?
          WHERE id = ?
      ");

      $fecha_ingreso = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_ingreso'])));
      $fecha_retiro = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_retiro'])));

      $stmt->bind_param("sssi", 
          $fecha_ingreso,
          $fecha_retiro,
          $_POST['motivo_retiro'],
          $empleado_id
      );

      if ($stmt->execute()) {
          // Redirigir al usuario al dashboard o mostrar un mensaje de éxito
          header('Location: dashboard.php');
          exit;
      } else {
          // Mostrar un mensaje de error si no se pudo actualizar el empleado
          echo "Error al crear el Paz y Salvo.";
          return false;
      }
  } else {
      // Mostrar un mensaje de error si no se pudo crear el Paz y Salvo
      echo "Error al crear el Paz y Salvo.";
      return false;
  }
}

private function guardarPazYSalvo($empleado_id) {
  $stmt = $this->conn->prepare("INSERT INTO paz_y_salvo (empleado_id, estado) VALUES (?, 'pendiente')"); // Agregar estado inicial
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

private function guardarEmpleado($usuario_id) {
  // No se necesita guardar el empleado, ya que existe en la tabla usuarios
  // Obtener los datos del usuario
  $usuario = getUserById($usuario_id);

  // Verificar si el usuario ya tiene un Paz y Salvo
  $paz_y_salvo_id = $this->obtenerPazYSalvoId($usuario_id);

  if (!$paz_y_salvo_id) {
      // Si no existe, crear un nuevo Paz y Salvo
      $paz_y_salvo_id = $this->crearPazYSalvo($usuario_id);

      // Actualizar la información del empleado con los datos del formulario
      $stmt = $this->conn->prepare("
          UPDATE empleados SET 
              fecha_ingreso = ?, 
              fecha_retiro = ?, 
              motivo_retiro = ?
          WHERE id = ?
      ");

      $fecha_ingreso = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_ingreso'])));
      $fecha_retiro = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_retiro'])));

      $stmt->bind_param("sssi", 
          $fecha_ingreso,
          $fecha_retiro,
          $_POST['motivo_retiro'],
          $usuario_id
      );

      $stmt->execute();
  }

  return $usuario_id; // Devolver el ID del usuario
}
private function actualizarEmpleado($empleado_id) {
  $stmt = $this->conn->prepare("
      UPDATE empleados SET 
          nombres = ?,  // <-- Actualizar la columna 'nombres'
          apellidos = ?, // <-- Actualizar la columna 'apellidos'
          cedula = ?, 
          cargo = ?,
          area = ?, 
          fecha_ingreso = ?, 
          fecha_retiro = ?, 
          motivo_retiro = ?
      WHERE id = ?
  ");

  // Convertir fechas al formato yyyy-mm-dd antes de guardarlas
  $fecha_ingreso = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_ingreso'])));
  $fecha_retiro = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['fecha_retiro'])));

  $stmt->bind_param("sssssssi", 
      $_POST['nombres'],  // <-- Obtener el valor de 'nombres' del formulario
      $_POST['apellidos'], // <-- Obtener el valor de 'apellidos' del formulario
      $_POST['cedula'],
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
    $stmt = $this->conn->prepare("SELECT COUNT(*) FROM paz_y_salvo p JOIN empleados e ON p.empleado_id = e.id WHERE e.cedula = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
  }

  private function guardarFirmas($paz_y_salvo_id) {
    // Consulta para insertar o actualizar las firmas
    $stmt = $this->conn->prepare("
        INSERT INTO firmas (
            paz_y_salvo_id,
            departamento,
            nombre_firmante,
            fecha_firma,
            imagen_firma,
            descuento,
            descripcion_descuento,
            a_paz_y_salvo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            nombre_firmante = VALUES(nombre_firmante),
            fecha_firma = VALUES(fecha_firma),
            imagen_firma = VALUES(imagen_firma),
            descuento = VALUES(descuento),
            descripcion_descuento = VALUES(descripcion_descuento),
            a_paz_y_salvo = VALUES(a_paz_y_salvo)
    ");

    foreach ($this->departments as $index => $dept) {
        if (isset($_FILES["firma_dept_$index"]) &&
            $_FILES["firma_dept_$index"]['error'] === 0 &&
            !empty($_POST["nombre_firmante_$index"]) &&
            !empty($_POST["fecha_firma_$index"])
        ) {
            $firma = file_get_contents($_FILES["firma_dept_$index"]['tmp_name']);

            // Convertir la fecha al formato yyyy-mm-dd
            $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_POST["fecha_firma_$index"])));

            // Obtener el descuento y la descripción del formulario como texto
            $descuento = isset($_POST["descuento_$index"]) ? $_POST["descuento_$index"] : '0';
            $descripcion_descuento = isset($_POST["descripcion_descuento_$index"]) ? $_POST["descripcion_descuento_$index"] : '';
            // Obtener el valor de a_paz_y_salvo
            $a_paz_y_salvo = isset($_POST["a_paz_y_salvo_$index"]) ? $_POST["a_paz_y_salvo_$index"] : '';

            $stmt->bind_param("isssssss",
                $paz_y_salvo_id,
                $dept,
                $_POST["nombre_firmante_$index"],
                $fecha,
                $firma,
                $descuento,
                $descripcion_descuento,
                $a_paz_y_salvo
            );

            $stmt->execute();
        } 
        // Si no se envía una nueva firma, pero ya existe una en la base de datos, 
        // mantener la existente
        elseif (!empty($_POST["nombre_firmante_$index"]) &&
            !empty($_POST["fecha_firma_$index"])) {
            
            // Convertir la fecha al formato yyyy-mm-dd
            $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $_POST["fecha_firma_$index"])));

            // Obtener el descuento y la descripción del formulario como texto
            $descuento = isset($_POST["descuento_$index"]) ? $_POST["descuento_$index"] : '0';
            $descripcion_descuento = isset($_POST["descripcion_descuento_$index"]) ? $_POST["descripcion_descuento_$index"] : '';
            // Obtener el valor de a_paz_y_salvo
            $a_paz_y_salvo = isset($_POST["a_paz_y_salvo_$index"]) ? $_POST["a_paz_y_salvo_$index"] : '';

            // Actualizar la firma (nombre, fecha, descuento, descripción y a_paz_y_salvo)
            $stmt_update = $this->conn->prepare("
                UPDATE firmas SET 
                    nombre_firmante = ?, 
                    fecha_firma = ?, 
                    descuento = ?, 
                    descripcion_descuento = ?, 
                    a_paz_y_salvo = ? 
                WHERE paz_y_salvo_id = ? AND departamento = ?
            ");
            $stmt_update->bind_param("ssssssi", 
                $_POST["nombre_firmante_$index"], 
                $fecha, 
                $descuento, 
                $descripcion_descuento, 
                $a_paz_y_salvo,
                $paz_y_salvo_id, 
                $dept
            );
            $stmt_update->execute();
            $stmt_update->close();
        }
    }
}


  private function actualizarEstadoPazYSalvo($paz_y_salvo_id) {
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
    }
    elseif ($num_firmas == count($this->departments)) {
      $estado = 'completado';
    }
    else {
      $estado = 'en_proceso';
    }
  
    // Actualizar el estado en la tabla paz_y_salvo (CORREGIDO)
    $stmt = $this->conn->prepare("UPDATE paz_y_salvo SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $paz_y_salvo_id);
    $stmt->execute();
    $stmt->close();
  }
  
  private function obtenerEstadoPazYSalvo($paz_y_salvo_id) {
    $stmt = $this->conn->prepare("SELECT estado FROM paz_y_salvo WHERE id = ?");
    $stmt->bind_param("i", $paz_y_salvo_id);
    $stmt->execute();
    $stmt->bind_result($estado);
    $stmt->fetch();
    $stmt->close();
    return $estado;
  }

  public function visualizarPazYSalvo($empleado_id) {
    return $this->generarPDF($empleado_id, 'I'); // Para mostrar en navegador
}

public function generarPDFPublico($empleado_id) { 
    return $this->generarPDF($empleado_id, 'S'); // Para retornar como string
}

private function generarPDF($empleado_id, $output = 'S') {
    $pdf = new PazYSalvoPDF();
    
    $stmt = $this->conn->prepare("SELECT nombres, apellidos FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empleado = $result->fetch_assoc();
    
    $nombreCompleto = $empleado['nombres'] . ' ' . $empleado['apellidos'];
    $pdf->setNombreEmpleado($nombreCompleto);
    
    // Configurar márgenes
    $pdf->SetMargins(30, 30, 30);
    
    $pdf->SetFont('Arial', '', 11);
    
    // Calcular el ancho efectivo y la posición
    $pageWidth = $pdf->GetPageWidth();
    $textWidth = 172; // Ancho deseado del texto
    $leftMargin = ($pageWidth - $textWidth) / 2; // Centrar horizontalmente
    
    // Establecer la posición X
    $pdf->SetX($leftMargin);
    
    // Usar el ancho específico para el MultiCell
    $pdf->MultiCell($textWidth, 5, $this->getEmpleadoInfo($empleado_id), 0, 'J');
    
    $paz_y_salvo_id = $this->obtenerPazYSalvoId($empleado_id);
    $firmas = $this->obtenerFirmasPazYSalvo($paz_y_salvo_id);
    $this->agregarFirmasDepartamentos($pdf, $firmas);
    
    $filename = 'paz_y_salvo_' . $empleado_id . '_' . date('Y-m-d') . '.pdf';
    
    return $pdf->Output($output, $filename);
}



public function getEmpleadoInfoPublico($empleado_id) {
    $stmt = $this->conn->prepare("SELECT nombres, apellidos, cedula FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empleado = $result->fetch_assoc();
    $stmt->close();

    return $empleado; // Devuelve un array con la información
}

private function getEmpleadoInfo($empleado_id) {
    $stmt = $this->conn->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $empleado_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empleado = $result->fetch_assoc();
    $stmt->close();

    // Formatear las fechas
    $fechaIngreso = date('d/m/Y', strtotime($empleado['fecha_ingreso']));
    $fechaRetiro = date('d/m/Y', strtotime($empleado['fecha_retiro']));

    return utf8_decode(
sprintf(
    "Entre los suscritos a saber, por una parte POLIGROW COLOMBIA SAS en calidad de empleador y por otra %s en calidad de %s, identificado con documento No. %s se celebró contrato laboral el día %s, contrato que a fecha %s finaliza por concepto de %s, así las cosas, se firma la presente paz y salvo con las siguientes manifestaciones.",
    $empleado['nombres'] . ' ' . $empleado['apellidos'],
    $empleado['cargo'],
    $empleado['cedula'],
    $fechaIngreso,
    $fechaRetiro,
    $empleado['motivo_retiro']
)
    );
}

private function agregarFirmasDepartamentos($pdf, $firmas) {
    $pdf->Ln(1);

    $col_width = 85; // Ancho ligeramente reducido
    $row_height = 30; // Mantener la altura original
    $x_start = 20;
    $x_spacing = 85; // Mantener el espaciado original
    $y_start = $pdf->GetY();
    $y_spacing = 30; // Mantener el espaciado entre filas

    $num_firmas = count($this->departments);
    $num_cols = 2;
    $num_rows = ceil($num_firmas / $num_cols);

    for ($i = 0; $i < $num_firmas; $i++) {
        $col = $i % $num_cols;
        $row = floor($i / $num_cols);
        $x_pos = $x_start + ($col * $x_spacing);
        $y_pos = $y_start + ($row * $y_spacing);

        $pdf->SetXY($x_pos, $y_pos);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($col_width, 4, $this->departments[$i], 1, 2, 'C');

        $firma_encontrada = false;
        foreach ($firmas as $firma) {
            if ($firma['departamento'] === $this->departments[$i]) {
                $firma_encontrada = true;
                $imagen_firma = $firma['imagen_firma'];
                $nombre_firmante = $firma['nombre_firmante'];
                $fecha_firma = $firma['fecha_firma'];
                $descuento = $firma['descuento'];
                $descripcion_descuento = $firma['descripcion_descuento'];
                $a_paz_y_salvo = isset($firma['a_paz_y_salvo']) ? $firma['a_paz_y_salvo'] : '';
                break;
            }
        }

        if ($firma_encontrada) {
            $temp_filename = tempnam(sys_get_temp_dir(), 'firma_') . '.png';
            file_put_contents($temp_filename, $imagen_firma);

            // Reducir el tamaño de la firma
            $firma_width = 47; // Ancho reducido
            $firma_height = 14; // Altura reducida

            $x_firma = $x_pos + ($col_width - $firma_width) / 2;
            $y_firma = $y_pos + 6; // Mantener la posición vertical

            $pdf->Image(
                $temp_filename,
                $x_firma,
                $y_firma,
                $firma_width,
                $firma_height
            );


            unlink($temp_filename);

// Iniciar la sección de Pago y Descuento
$pdf->SetXY($x_pos, $y_firma + $firma_height - 2);
$pdf->SetFont('Arial', 'B', 9);

// Imprimir "Pago" en negrita
$pdf->Cell($col_width * 0.1, 6, 'Pago: ', 0, 0, 'L');

// Imprimir el valor de pago sin negrita
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($col_width * 0.2, 6, $descuento, 0, 1, 'L');

// Imprimir "Descuento" en negrita
$pdf->SetX($x_pos); // Resetear la posición X
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell($col_width * 0.1, 6, 'Descuento: ', 0, 0, 'L');

// Imprimir la descripción del descuento sin negrita, movida ligeramente a la derecha
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($col_width * 0.08, 6, '', 0, 0, 'L'); // Ajuste más pequeño
$pdf->Cell($col_width * 0.25, 6, $descripcion_descuento, 0, 1, 'L');

// Iniciar la sección del nombre y la fecha del firmante
$pdf->SetXY($x_pos + $col_width * 0.7, $y_firma + $firma_height - 2); // Mover a la derecha
$pdf->SetFont('Arial', '', 8.5);
$nombre_firmante = substr($nombre_firmante, 0, 9);
$pdf->Cell($col_width * 0.6, 4, $nombre_firmante, 0, 1, 'L');

// Imprimir la fecha debajo del nombre
$pdf->SetX($x_pos + $col_width * 0.4); // Mover a la derecha
$pdf->Cell($col_width * 0.5, 4, $fecha_firma, 0, 1, 'R');

// Imprimir "a paz y salvo" debajo de la fecha
$pdf->SetX($x_pos + $col_width * 0.7); // Mantener la misma posición X
$pdf->Cell($col_width * 0.6, 4, 'Paz y Salvo: ' . $a_paz_y_salvo, 0, 1, 'L');

$pdf->Rect($x_pos, $y_pos, $col_width, $row_height);
        }
    }
}




  public function getDepartments() {
    return $this->departments;
  }
  }
  // Obtener el ID del empleado y el ID del Paz y Salvo (si se proporcionan en la URL)
  $empleado_id = isset($_GET['empleado_id']) ? $_GET['empleado_id'] : null;
  $paz_y_salvo_id = isset($_GET['paz_y_salvo_id']) ? $_GET['paz_y_salvo_id'] : null;
  
  
  // Cargar la información del empleado y las firmas si se está editando
  if ($empleado_id) {
  $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM empleados WHERE id = ?");
  $stmt->bind_param("i", $empleado_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $empleado = $result->fetch_assoc();
  $stmt->close();
  
  // Cargar las firmas (usando el ID del Paz y Salvo)
  $stmt = DatabaseConfig::getConnection()->prepare("SELECT * FROM firmas WHERE paz_y_salvo_id = ?");
  $stmt->bind_param("i", $paz_y_salvo_id); 
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