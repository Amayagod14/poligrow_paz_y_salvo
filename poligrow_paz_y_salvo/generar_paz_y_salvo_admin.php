<?php
require_once 'includes/database.php';
require_once 'logica_paz_y_salvo.php';

$pazYSalvo = new PazYSalvo(); // Inicializar la clase PazYSalvo

// Obtener el ID del empleado y el ID del Paz y Salvo (si se proporcionan en la URL)
$empleado_id = isset($_GET['empleado_id']) ? $_GET['empleado_id'] : null;
$paz_y_salvo_id = isset($_GET['paz_y_salvo_id']) ? $_GET['paz_y_salvo_id'] : null;

// Cargar la información del empleado si se está editando
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
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
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
                        // Mostrar alerta con SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El archivo ' + file.name + ' excede el tamaño máximo permitido de 2MB'
                        });
                    }

                    if (!file.type.startsWith('image/')) {
                        isValid = false;
                        // Mostrar alerta con SweetAlert2
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'El archivo ' + file.name + ' debe ser una imagen'
                        });
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

    // Simular la selección de archivos en los campos de firma
    $('input[type="file"]').each(function() {
        const firmaData = $(this).data('firma');
        if (firmaData) {
            // Convertir la imagen base64 a un objeto Blob
            const byteCharacters = atob(firmaData.split(',')[1]);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], {type: 'image/png'});

            // Crear un objeto File a partir del Blob
            const file = new File([blob], 'firma.png', {type: 'image/png'});

            // Simular la selección del archivo en el campo de input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            this.files = dataTransfer.files;

            // Mostrar el nombre del archivo en el campo de input (opcional)
            $(this).parent().find('.file-name').text(file.name);
        }
    });
});
</script>
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

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="empleado_id" id="empleado_id" value="<?php echo $empleado_id; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombres</label>
                    <input type="text" name="nombres" required
                           value="<?php echo isset($empleado['nombres']) ? $empleado['nombres'] : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" name="apellidos" required
                           value="<?php echo isset($empleado['apellidos']) ? $empleado['apellidos'] : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cédula</label>
                    <input type="text" name="cedula" required
                           value="<?php echo isset($empleado['cedula']) ? $empleado['cedula'] : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cargo</label>
                    <input type="text" name="cargo" required
                           value="<?php echo isset($empleado['cargo']) ? $empleado['cargo'] : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Área</label>
                    <input type="text" name="area" required
                           value="<?php echo isset($empleado['area']) ? $empleado['area'] : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Motivo de retiro</label>
                    <select name="motivo_retiro" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                        <option value="RENUNCIA" <?php echo (isset($empleado['motivo_retiro']) && $empleado['motivo_retiro'] == 'RENUNCIA') ? 'selected' : ''; ?>>Renuncia</option>
                        <option value="TERMINACION DE CONTRATO" <?php echo (isset($empleado['motivo_retiro']) && $empleado['motivo_retiro'] == 'TERMINACION DE CONTRATO') ? 'selected' : ''; ?>>Terminación de contrato</option>
                        <option value="MUTUO ACUERDO" <?php echo (isset($empleado['motivo_retiro']) && $empleado['motivo_retiro'] == 'MUTUO ACUERDO') ? 'selected' : ''; ?>>Mutuo acuerdo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de ingreso</label>
                    <input type="text" name="fecha_ingreso" required class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="<?php echo isset($empleado['fecha_ingreso']) ? date('d/m/Y', strtotime($empleado['fecha_ingreso'])) : ''; ?>" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de retiro</label>
                    <input type="text" name="fecha_retiro" required class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="<?php echo isset($empleado['fecha_retiro']) ? date('d/m/Y', strtotime($empleado['fecha_retiro'])) : ''; ?>" readonly>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <?php
                // Obtener la información del usuario
                $usuario = getUserById($_SESSION['user_id']);
                $departamento_usuario = $usuario['area'];

                foreach ($pazYSalvo->getDepartments() as $index => $dept):
                    if ($dept === $departamento_usuario):
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
                                           data-firma="<?php echo isset($firma['imagen_firma']) ? base64_encode($firma['imagen_firma']) : ''; ?>"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        <?php echo ($firma && $firma['imagen_firma']) ? 'style="display: none;"' : ''; ?>>

                                    <input type="hidden" name="firma_base64_<?php echo $index; ?>" value="<?php echo isset($firma['imagen_firma']) ? base64_encode($firma['imagen_firma']) : ''; ?>">

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
                                <div> 
                                    <label class="block text-sm font-medium text-gray-700">Descuento</label>
                                    <input type="number" name="descuento_<?php echo $index; ?>" step="0.01" min="0"
                                           value="<?php echo isset($firma['descuento']) ? $firma['descuento'] : '0.00'; ?>"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción del descuento</label>
                                    <textarea name="descripcion_descuento_<?php echo $index; ?>"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo isset($firma['descripcion_descuento']) ? $firma['descripcion_descuento'] : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 flex justify-center space-x-4">
                <button type="submit" name="guardar_y_salir"
                        class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Guardar información
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
</body>
</html>