<div>
    @php
        if ($leave->document != '') {
            $info = new SplFileInfo($leave->document);
            $extension = $info->getExtension();

            if (
                $extension === 'png' ||
                $extension === 'jpg' ||
                $extension === 'jpeg' ||
                $extension === 'PNG' ||
                $extension === 'JPG' ||
                $extension === 'JPEG'
            ) {
                echo '<img src="' . asset('uploads/leave_document/' . $leave->document) . '" width="100%">';
            } else {
                echo '<embed src="' .
                    asset('uploads/leave_document/' . $leave->document) .
                    '" width="100%" height="100%" />';
            }
        }
    @endphp
</div>
