<?php

    $id = trim($_GET['id']);
    $label = trim($_GET['label']);

    if (empty($id) || empty($label))
    {
        echo '<html />';
        return;
    }
    
    $img = 'http://www.googleadservices.com/pagead/conversion/' . $id . '/?label=' . $label . '&guid=ON&script=0';    
?>

<html>
    <head>
        <title>SpatialMatch AdWords Conversion</title>

        <script type='text/javascript'>
            var google_conversion_id = '<?php echo $id ?>';
            var google_conversion_language = 'en';
            var google_conversion_format = '2';
            var google_conversion_color = 'ffffff';
            var google_conversion_label = '<?php echo $label ?>';
            var google_conversion_value = 0;
        </script>

    </head>
    
    <body>
        <script type='text/javascript' src='http://www.googleadservices.com/pagead/conversion.js'></script>
    
        <noscript>
            <img src='<?php echo $img ?>' />
        </noscript>
        
        <br />
        
        ok.
    </body>
    
</html>
