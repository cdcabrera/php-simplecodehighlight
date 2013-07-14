<?php include_once("simplecodehighlight.php"); ?>
<!doctype html>
<!--[if lt IE 8]> <html class="no-js lt-ie10 lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie10 lt-ie9" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <title>PHP.Simple Code Highlight</title>
        <style>
            /*-- Normalize, http://necolas.github.com/normalize.css/ - - - - - - - - - - - - - - - - - - - --*/
            article,aside,details,figcaption,figure,footer,header,hgroup,nav,section,summary{display:block}audio,canvas,video{display:inline-block}audio:not([controls]){display:none;height:0}[hidden]{display:none}html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}body{margin:0}a:focus{outline:thin dotted}a:active,a:hover{outline:0}h1{font-size:2em}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:700}dfn{font-style:italic}mark{background:#ff0;color:#000}code,kbd,pre,samp{font-family:monospace,serif;font-size:1em}pre{white-space:pre;white-space:pre-wrap;word-wrap:break-word}q{quotes:"\201c" "\201d" "\2018" "\2019"}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-.5em}sub{bottom:-.25em}img{border:0}svg:not(:root){overflow:hidden}figure{margin:0}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend{border:0;padding:0}button,input,select,textarea{font-family:inherit;font-size:100%;margin:0}button,input{line-height:normal}button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer}button[disabled],input[disabled]{cursor:default}input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}textarea{overflow:auto;vertical-align:top}table{border-collapse:collapse;border-spacing:0}

            /*-- Box Modal tweak, http://www.paulirish.com/2012/box-sizing-border-box-ftw/ --*/
            * { -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; }

            body                            {   background-color:#eceff1; }

            ul                              {   padding:10px 0; margin:0; background-color:#ffffff; }
            ul li                           {   padding:4px 0; }
            ul strong                       {   display:inline-block; width:150px; margin-right:8px; text-align:right; }

            code                            {   font-family:monospace,serif; }
            code ol                         {   color:#9ca3a8; background-color:#ffffff; margin:0 0 0 60px; padding:0; }

            code ol li                      {   padding:4px 0; min-height:25px; white-space:nowrap; }
            code ol li:nth-child(odd)       {   background-color:#eceff1; }

            code ol div                     {   color:#316e99; }

            .code-comm                      {   color:#9ca3a8; }
            .code-oper                      {   color:#007700; }
            .code-str                       {   color:#dd1144; }
            .code-block                     {   color:#007700; }
        </style>
    </head>
    <body>

        <?php
            //-- Basic setup
            $CodeHighlight = new SimpleCodeHighlight();

            //-- Caching is optional, remember to set your directory permissions if used
            //$CodeHighlight->CacheDirectory = "/[SOME PATH TO YOUR CACHE DIRECTORY]/";

            //-- Returned object in the form of
            /*
             (
             "success"      => boolean/null,
             "raw"          => string/null,
             "parsed"       => array,
             "cachename"    => string/null,
             "cachepath"    => string/null,
             "cacheupdated" => boolean/null
             )
            */
            $Data = $CodeHighlight->Get("[SOME URL TO YOUR CODE/TEXT FILE]");
        ?>

        <ul>
            <?php
                $HTMLString =  "<li><strong>success = </strong>". (($Data->success)?"true":"false") ."</li>".
                               "<li><strong>cachename = </strong>". $Data->cachename ."</li>".
                               "<li><strong>cachepath = </strong>". $Data->cachepath ."</li>".
                               "<li><strong>cacheupdated = </strong>". (($Data->cacheupdated)?"true":"false") ."</li>";

                echo $HTMLString;
            ?>
        </ul>

        <code>
            <ol>
                <?php
                    foreach( $Data->parsed as $Line )
                    {
                        echo '<li><div>'.$Line.'</div></li>';
                    }
                ?>
            </ol>
        </code>
    </body>
</html>