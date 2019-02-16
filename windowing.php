<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']) {
    require __DIR__.'/autoload.php';

    function sendResponse($message = null, $success = false, array $data = null) {
        if (!$message) {
            $message = 'Unknown error!';
        }

        echo json_encode(compact('success', 'message', 'data'));
        die;
    }

    function post($name, $default = null) {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    set_error_handler(function($errno, $errstr) {
        sendResponse($errstr);
    });

    set_exception_handler(function($e) {
        sendResponse($e->getMessage());
    });

    $windowing = new Fal\Windowing\Windowing();
    $windowing->setNgramValue(post('ngram', 2));
    $windowing->setPrimeNumber(post('prime', 3));
    $windowing->setNWindowValue(post('window', 4));

    $result = $windowing->compare(post('source', ''), post('comparator', ''));

    sendResponse(true, 'Success', array(
        'percentage' => $result->getPercentage(),
        'coefficient' => $result->getCoefficient(),
        'ngram1' => $result->getSource()->getNGram(),
        'ngram2' => $result->getComparator()->getNGram(),
    ));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Windowing Algorithm</title>
    <style>
        body {
            padding: 0;
            margin: 0;
        }
        .panel {
            margin: 10px;
            display: flex;
        }
        .panel-item {
            flex: 1;
            border: solid 1px #999;
            border-collapse: collapse;
            padding: 10px;
        }
        .source-text {
            width: 100%;
            height: 100px;
            box-sizing: border-box;
        }
        .submit {
            text-align: right;
            background: khaki;
            padding: 10px;
            margin: 10px 0;
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
        table {
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
        .result-detail table tbody tr:hover {
            background: khaki;
            cursor: default;
        }
        .result-detail table td {
            padding: 3px 4px;
        }
        #similarity {
            font-weight: bold;
            font-size: larger;
            color: blue;
        }
    </style>
</head>
<body>
    <div class="panel">
        <div class="panel-item">
            <h3>Input</h3>
            <hr>

            <form method="post" id="form">
                <div class="submit">
                    <button type="submit">Compare</button>
                    <button type="reset">Reset</button>
                </div>

                <label for="text1">Text 1 (Source)</label>
                <br>
                <textarea name="text1" id="text1" class="source-text">Indonesia Raya</textarea>
                <br>
                <br>

                <label for="text2">Text 2 (Comparator)</label>
                <br>
                <textarea name="text2" id="text2" class="source-text">Indonesia Jaya</textarea>
                <br>
                <br>

                <label for="ngram">N-Gram</label>
                <input type="number" name="ngram" id="ngram" value="2">
                <br>
                <br>

                <label for="prime">Prime Number</label>
                <input type="number" name="prime" id="prime" value="3">
                <br>
                <br>

                <label for="window">N-Window</label>
                <input type="number" name="window" id="window" value="4">

                <div class="submit">
                    <button type="submit">Compare</button>
                    <button type="reset">Reset</button>
                </div>
            </form>
        </div>

        <div class="panel-item">
            <h3>Result</h3>
            <hr>

            <div id="status-bar"></div>

            <table class="result-detail">
                <thead>
                    <tr>
                        <th>Similarity</th>
                        <th id="similarity">&nbsp;</th>
                    </tr>
                    <tr>
                        <th>Jaccard Coefficient</th>
                        <th id="coefficient">&nbsp;</th>
                    </tr>
                    <tr><th colspan="2">&nbsp;</th></tr>
                    <tr>
                        <th>N-Gram</th>
                        <td>
                            <table id="ngram-result">
                                <thead>
                                    <tr>
                                        <th>Source</th>
                                        <th>Comparator</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2"><em>No data</em></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div> <!-- .panel -->

    <script>
        var form = document.getElementById('form');

        function onFormSubmit(e) {
            e.preventDefault();

            doPost(window.location.href, {
                source: document.getElementById('text1').value,
                comparator: document.getElementById('text2').value,
                ngram: document.getElementById('ngram').value,
                prime: document.getElementById('prime').value,
                window: document.getElementById('window').value,
            }, function(xhr) {
                var response = JSON.parse(xhr.responseText);

                if (response.success) {
                    document.getElementById('similarity').innerHTML = response.data.percentage + '%';
                    document.getElementById('coefficient').innerHTML = response.data.coefficient;

                    var max = Math.max(response.data.ngram1.length, response.data.ngram2.length);
                    var ngramWrapper = document.getElementById('ngram-result').querySelector('tbody');
                    ngramWrapper.innerHTML = '<tr><td colspan="2">Total length: ' + max + '</td></tr>';

                    for (var i = 0; i < max; i++) {
                        var row = document.createElement('tr');
                        var c1 = document.createElement('td');
                        var c2 = document.createElement('td');

                        c1.innerHTML = response.data.ngram1[i] || '';
                        c2.innerHTML = response.data.ngram2[i] || '';

                        row.appendChild(c1);
                        row.appendChild(c2);
                        ngramWrapper.appendChild(row);
                    }
                } else {
                    alert('Response error: ' + response.message);
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
