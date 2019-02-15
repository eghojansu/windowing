<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']) {
    require __DIR__.'/src/Windowing/Result.php';
    require __DIR__.'/src/Windowing/Source.php';
    require __DIR__.'/src/Windowing/Windowing.php';

    $response = array(
        'success' => false,
        'message' => 'Unknown error!',
        'data' => array(),
    );

    $source = isset($_POST['source']) ? $_POST['source'] : '';
    $comparator = isset($_POST['comparator']) ? $_POST['comparator'] : '';

    try {
        $windowing = new Fal\Windowing\Windowing();
        $result = $windowing->compare($source, $comparator);

        $response['success'] = true;
        $response['message'] = 'Success';
        $response['data']['percentage'] = $result->getPercentage();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    die;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Windowing Algorithm</title>
    <style>
        .source-text {
            width: 90%;
            height: 100px;
        }
        #status-bar {
            min-height: 10px;
            font-weight: bold;
            font-size: larger;
            text-align: right;
            color: red;
        }
        #result {
            margin-top: 40px;
        }
        .result-detail {
            width: 100%;
            border-collapse: collapse;
        }
        .result-detail th,
        .result-detail td {
            text-align: left;
            vertical-align: top;
            border: solid 1px #999;
            padding: 5px 8px;
        }
        #similarity {
            font-weight: bold;
            font-size: larger;
            color: blue;
        }
    </style>
</head>
<body>
    <form method="post" id="form">
        <label for="text_1">Text 1</label>
        <br>
        <textarea name="text_1" id="text_1" class="source-text">Indonesia Raya</textarea>
        <br>
        <br>

        <label for="text_2">Text 2</label>
        <br>
        <textarea name="text_2" id="text_2" class="source-text">Indonesia Jaya</textarea>
        <br>
        <br>
        <button type="submit">Compare</button>
        <button type="reset">Reset</button>
    </form>

    <div id="result">
        <div id="status-bar"></div>

        <table class="result-detail">
            <thead>
                <tr>
                    <th>Similarity</th>
                    <th id="similarity" colspan="2">&nbsp;</th>
                </tr>
                <!--
                <tr><th colspan="2">&nbsp;</th></tr>
                <tr>
                    <th>&nbsp;</th>
                    <th>Source (Text 1)</th>
                    <th>Comparator (Text 2)</th>
                </tr>
                -->
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <script>
        var form = document.getElementById('form');

        function onFormSubmit(e) {
            e.preventDefault();

            doPost(window.location.href, {
                source: document.getElementById('text_1').value,
                comparator: document.getElementById('text_2').value,
            }, function(xhr) {
                var response = JSON.parse(xhr.responseText);

                if (response.success) {
                    document.getElementById('similarity').innerHTML = response.data.percentage + '%';
                } else {
                    alert('Parsing error: ' + response.message);
                }
            }, function(xhr) {
                alert('Request failed: ' + xhr.status + ' - ' + xhr.statusText);
            });
        }

        function doPost(url, data, onSuccess, onError) {
            var params = typeof data == 'string' ? data : Object.keys(data).map(
                    function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
                ).join('&');
            var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
            var status = document.getElementById('status-bar');

            status.innerHTML = 'Processing...';

            xhr.open('POST', url);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    status.innerHTML = '';

                    if (xhr.status == 200) {
                        onSuccess(xhr);
                    } else {
                        onError(xhr);
                    }
                }
            };
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(params);

            return xhr;
        }

        // attach handler to the keydown event of the document
        if (document.attachEvent) {
            form.attachEvent('onSubmit', onFormSubmit);
        } else {
            form.addEventListener('submit', onFormSubmit);
        }
    </script>
</body>
</html>
