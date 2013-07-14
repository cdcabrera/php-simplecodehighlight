<?php

    class SimpleCodeHighlight
    {
        public $CacheDirectory  = null;                             //-- string, directory you wish to store cache files
        public $CacheTime       = 3600;                             //-- int, seconds to cache files, default 1 hour
        public $CacheAll        = true;                             //-- boolean, cache internal and external files, false equates to only external files
        public $CacheMD5ID      = null;                             //-- string, provide a string for a MD5 hash file name. null equates to letting the default take over
        public $CacheFileName   = null;                             //-- string, instead of a MD5 hash generated name use this. null equates to letting a MD5 generated hash take over
        public $CacheExtension  = "txt";                            //-- string, the file extension for your cache files
        public $Spacing         = "&nbsp;";                         //-- string, html you want inserted to fill the blanks
        public $ReplaceTemplate = "<span class=\"{0}\">{1}</span>"; //-- string, html tokenized string. "{0}" is replaced with class, "{1}" is replaced with content

        public $FilterDefault   = array (                           //-- array of arrays, "class" is used in the tokenized string, "ignore" won't perform the find if detected, "find" matches your patterns
                                         array( "class"=>"code-block", "ignore"=>null, "find"=>"/(<script[^>]*>[\d\D]*?script>)|(<style[^>]*>[\d\D]*?style>)/"),
                                         array( "class"=>"code-comm",  "ignore"=>null, "find"=>"/(<!--[\d\D]*?-->)|(<!\[CDATA\[[\d\D]*?\]\]>)|(<!-->)|(\/\*[\s\S]*?\*\/|[\s](\/\/)[\s\S]*?\n)/" ),
                                         array( "class"=>"code-str",   "ignore"=>"/<\?php[\d\D]*?\?>/", "find"=>"/=[\s]*(\"[^\\\"]*(?:\\.[^\\\"]*)*\")/"),
                                         array( "class"=>"code-str",   "ignore"=>"/<[^\!][\d\D]*?>([\d\D]*?)<\/[\d\D]*?>/", "find"=>"/(\"[^\\\"]*(?:\\.[^\\\"]*)*\")|('[^\\']*(?:\\.[^\\']*)*')/"),
                                         array( "class"=>"code-oper",  "ignore"=>null, "find"=>"/(\+\+|\+=|\+|--|-=|-|&lt;&lt;=|&lt;&lt;|&lt;=|=&gt;|&gt;&gt;=|&gt;&gt;|&gt;=|!=|!|~|\^|\|\||&amp;&amp;|&amp;=|&amp;|\?\?|::|:|\*=|\*|\/=|%=|\|=|==|=)/" )
                                        );


        //-- start
        public function Get( $File = null )
        {
            $Data = $this->LoadFile($File);
            $Data->success = false;

            if(isset($Data->raw))
            {
                $Data->success = true;
                $Data->parsed = $this->ParseCode( $Data->raw );
            }

            return $Data;
        }


        //-- get the raw file
        private function LoadFile($File)
        {
            $Data       = (object) array("cachename"=>null, "cachepath"=>null, "cacheupdated"=>null, "raw"=>null, "parsed"=>array());
            $CachedFile = $this->GetCacheFile( $File );

            if( !$this->CheckFileExists($File) && is_null($CachedFile->data) )
            {
                return $Data;
            }

            $Data->cachename    = $CachedFile->cachename;
            $Data->cachepath    = $CachedFile->cachepath;
            $Data->cacheupdated = $CachedFile->update;

            if( isset($CachedFile->data) )
            {
                $Data->raw = $CachedFile->data;
            }
            elseif( $this->CheckFileExists($File) )
            {
                $Data->raw = $this->GetFileContents( $File );

                if( $Data->cacheupdated && isset($Data->raw) )
                {
                    file_put_contents($Data->cachepath, $Data->raw);
                }
            }

            return $Data;
        }


        //-- does the file even exist
        private function CheckFileExists( $File )
        {
            $return = file_exists($File);

            if( !$return && preg_match("/^(https?):\/\//i", $File) )
            {
                $header_response = get_headers($File);

                if ( strpos( $header_response[0], "200" ) !== false )
                {
                    $return = true;
                }
            }

            return $return;
        }


        ///-- grab cached file
        private function GetCacheFile( $File )
        {
            $CacheDirectory     = $this->CacheDirectory;
            $CacheTime          = $this->CacheTime;
            $CacheAll           = ($this->CacheAll)? $this->CacheAll : preg_match("/^(https?|ftp):\/\//i", $File);
            $CacheMD5ID         = $this->CacheMD5ID;
            $CacheFileName      = $this->CacheFileName;
            $CacheExtension     = $this->CacheExtension;
            $Data               = (object) array("data"=>null, "cachename"=>null, "cachepath"=>null, "update"=>null);

            if( !isset($CacheDirectory) || !$CacheAll )
            {
                return $Data;
            }

            $FileName           = "{0}.".$CacheExtension;
            $TempFileName       = md5($File.'_somestring');
            $FilePath           = $CacheDirectory;
            $FileExists         = false;

            if( isset($CacheFileName) )
            {
                $TempFileName = $CacheFileName;
            }
            elseif( isset($CacheMD5ID) )
            {
                $TempFileName = md5($CacheMD5ID);
            }

            $FileName =  preg_replace('/\{0\}/', $TempFileName, $FileName);

            $Data->cachename = $FileName;
            $Data->cachepath = $FilePath.$FileName;

            $FileExists = $this->CheckFileExists( $Data->cachepath );

            //-- use seconds until file is updated
            if( is_numeric( $CacheTime ) && $FileExists && (time() - $CacheTime < filemtime($Data->cachepath)) )
            {
                $Data->data = $this->GetFileContents( $Data->cachepath );
                $Data->update = false;
            }
            else
            {
                $Data->update = true;
            }

            return $Data;
        }


        //-- get file/url contents
        private function GetFileContents( $File )
        {
            if( strlen( $Data = file_get_contents($File) ) <= 0 )
            {
                $Curl = curl_init();
                curl_setopt($Curl,CURLOPT_URL,$File);
                curl_setopt($Curl,CURLOPT_RETURNTRANSFER,1);
                curl_setopt($Curl,CURLOPT_CONNECTTIMEOUT,1);
                $Data = curl_exec($Curl);
                curl_close($Curl);
            }

            return (strlen($Data) <= 0)? null : $Data;
        }


        //-- avoid nested labels
        private function MatchHasLabel( $Matches, $Labels )
        {
            foreach($Labels as $Label)
            {
                foreach($Matches as $Match)
                {
                    if( strpos( implode(" ", $Match), $Label ) !== false )
                    {
                        return true;
                    }
                }
            }
            return false;
        }


        //-- parse raw data
        private function ParseCode( $Data )
        {
            $Filters        = $this->FilterDefault;
            $Template       = $this->ReplaceTemplate;
            $Spacing        = $this->Spacing;
            $LabelNewLine   = "{codehighlight_".rand()."_newlinelabel}";
            $Matches        = array();
            $AvoidLabels    = array();
            $Return         = $Data;

            foreach( $Filters as $Filter )
            {
                if( !isset($Filter["ignore"]) || !preg_match($Filter["ignore"], $Return) )
                {
                    $TempLabel  = "{codehighlight_".rand()."_genericlabel}";
                    $TempClass  = $Filter["class"];
                    $TempRegEx  = $Filter["find"];
                    $TempArray  = array();

                    array_push($AvoidLabels, $TempLabel);
                    preg_match_all($TempRegEx, $Return, $TempArray, PREG_SET_ORDER);

                    if( !$this->MatchHasLabel($TempArray, $AvoidLabels) ) //-- avoid nested labels
                    {
                        $Return = preg_replace($TempRegEx, $TempLabel, $Return);
                        array_push($Matches, array($TempClass, $TempLabel, $TempArray));
                    }
                }
            }

            $Return = htmlentities($Return, ENT_QUOTES, "UTF-8");
            $Return = preg_replace(array("/\n/", "/\s/"), array($LabelNewLine, $Spacing), $Return);

            foreach( $Matches as $Match )
            {
                foreach($Match[2] as $SubMatch)
                {
                    $TempArray  = explode("\n", $SubMatch[0]);
                    $TempString = "";
                    $TempCount  = 0;

                    foreach($TempArray as $TempArrayMatch)
                    {
                        $TempArrayMatch = htmlentities($TempArrayMatch, ENT_QUOTES, "UTF-8");
                        $TempArrayMatch = preg_replace("/\s/", $Spacing, $TempArrayMatch);

                        if(strlen($TempArrayMatch)>0)
                        {
                            $TempString .= preg_replace('/\{1\}/', $TempArrayMatch, preg_replace('/\{0\}/', $Match[0], $Template));
                        }

                        if( count($TempArray) > 1 && !($TempCount >= count($TempArray)-1))
                        {
                            $TempString .= $LabelNewLine;
                        }
                        $TempCount += 1;
                    }
                    $Return = preg_replace("/".$Match[1]."/", $TempString, $Return, 1);
                }
            }


            $Return = explode($LabelNewLine, $Return);

            if( strlen($Return[count($Return)-1]) == 0 )
            {
                unset($Return[count($Return)-1]);
            }

            return $Return;
        }
    }

?>