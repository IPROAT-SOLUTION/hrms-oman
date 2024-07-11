<div>
    @php
        if ($employee->$key != '') {
            $info = new SplFileInfo($employee->$key);
            $extension = $info->getExtension();

            if (
                $extension === 'png' ||
                $extension === 'jpg' ||
                $extension === 'jpeg' ||
                $extension === 'PNG' ||
                $extension === 'JPG' ||
                $extension === 'JPEG'
            ) {
                echo '<img src="' . asset('uploads/employeeDocuments/' . $employee->$key) . '" width="100%">';
            } else {
                echo '<embed src="' .
                    asset('uploads/employeeDocuments/' . $employee->$key) .
                    '" width="100%" height="100%" />';
            }
        }
    @endphp
</div>
