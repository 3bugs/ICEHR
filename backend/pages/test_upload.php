<?php
if (!empty($_FILES) || !empty($_POST)) {
    /*$output = "***** POST *****\n" . print_r($_POST, true);
    $output .= "\n***** FILES *****\n" . print_r($_FILES, true);*/
    print_r($_POST);
    print_r($_FILES);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        #selectedFiles img {
            max-width: 200px;
            max-height: 200px;
            float: left;
            margin-bottom:10px;
        }
    </style>
</head>
<body>

<?php
?>

<form id="formAddNews" method="post">
    <div id="output"></div>
    Files: <input type="file" id="files" multiple><br/>

    <div id="selectedFiles"></div>
    <input type="text" name="first_name" value="test"/>

    <input name="submit" type="submit">
</form>

<!--<script type="text/javascript"
        src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"/>-->
<?php require_once('../include/head.inc'); ?>
<!--jQuery Form Plugin-->
<script src="../dist/js/jquery.form.js"></script>

<script>
    let selDiv = "";
    let storedFiles = [];

    $(document).ready(function() {
        $("#files").on("change", handleFileSelect);

        selDiv = $("#selectedFiles");
        $("#formAddNews").on("submit", handleForm);

        $("body").on("click", ".selFile", removeFile);
    });

    function handleFileSelect(e) {
        let files = e.target.files;
        let filesArr = Array.prototype.slice.call(files);

        filesArr.forEach(function(f) {
            if (!f.type.match("image.*")) {
                return;
            }
            storedFiles.push(f);

            let reader = new FileReader();
            reader.onload = function (e) {
                let html = "<div><img src=\"" + e.target.result + "\" data-file='"+f.name+"' class='selFile' title='Click to remove'>" + f.name + "<br clear=\"left\"/></div>";
                selDiv.append(html);

            };
            reader.readAsDataURL(f);
        });
    }

    function handleForm(e) {
        e.preventDefault();

        const form = $('#formAddNews')[0];
        const formData = new FormData(form);

        for (let i = 0, len = storedFiles.length; i < len; i++) {
            formData.append('files[]', storedFiles[i]);
        }

        $.ajax({
            url: '<?= $_SERVER['PHP_SELF']; ?>',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success: function(result) {
                //alert(result);
                $('#output').text(result);
            },
            error: function() {
                alert('Error');
            }
        });

        /*let xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);

        xhr.onload = function(e) {
            if (this.status == 200) {
                /!*console.log(e.currentTarget.responseText);
                alert(e.currentTarget.responseText + ' items uploaded.');*!/
                //alert(this.response);
                $('#output').text(this.response);
            }
        };
        xhr.send(formData);*/



        /*let data = new FormData();

        for (let i = 0, len = storedFiles.length; i < len; i++) {
            data.append('files', storedFiles[i]);
        }

        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'handler.cfm', true);

        xhr.onload = function(e) {
            if (this.status == 200) {
                console.log(e.currentTarget.responseText);
                alert(e.currentTarget.responseText + ' items uploaded.');
            }
        };

        xhr.send(data);*/
    }

    function removeFile(e) {
        let file = $(this).data("file");
        for (let i = 0; i < storedFiles.length; i++) {
            if (storedFiles[i].name === file) {
                storedFiles.splice(i,1);
                break;
            }
        }
        $(this).parent().remove();
    }
</script>
</body>
</html>