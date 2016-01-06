<?php namespace Boofw\Phpole\Helper;

class File
{
    static $mimes = array(
        'application/SLA'=>'stl',
        'application/STEP'=>'stp',
        'application/acad'=>'dwg',
        'application/andrew-inset'=>'ez',
        'application/clariscad'=>'ccad',
        'application/drafting'=>'drw',
        'application/dsptype'=>'tsp',
        'application/dxf'=>'dxf',
        'application/i-deas'=>'unv',
        'application/mac-binhex40'=>'hqx',
        'application/mac-compactpro'=>'cpt',
        'application/mspowerpoint'=>'ppt',
        'application/msword'=>'doc',
        //'application/octet-stream'=>'exe',
        'application/oda'=>'oda',
        'application/pdf'=>'pdf',
        'application/postscript'=>'ai',
        'application/pro_eng'=>'prt',
        'application/set'=>'set',
        'application/smil'=>'smi',
        'application/solids'=>'sol',
        'application/vda'=>'vda',
        'application/vnd.mif'=>'mif',
        'application/vnd.ms-excel'=>'xls',
        'application/x-bcpio'=>'bcpio',
        'application/x-cdlink'=>'vcd',
        'application/x-chess-pgn'=>'pgn',
        'application/x-cpio'=>'cpio',
        'application/x-csh'=>'csh',
        'application/x-director'=>'dcr',
        'application/x-dvi'=>'dvi',
        'application/x-freelance'=>'pre',
        'application/x-futuresplash'=>'spl',
        'application/x-gtar'=>'gtar',
        'application/x-gzip'=>'gz',
        'application/x-hdf'=>'hdf',
        'application/x-ipix'=>'ipx',
        'application/x-ipscript'=>'ips',
        'application/x-javascript'=>'js',
        'application/x-koan'=>'skt',
        'application/x-latex'=>'latex',
        'application/x-lisp'=>'lsp',
        'application/x-lotusscreencam'=>'scm',
        'application/x-netcdf'=>'cdf',
        'application/x-sh'=>'sh',
        'application/x-shar'=>'shar',
        'application/x-shockwave-flash'=>'swf',
        'application/x-stuffit'=>'sit',
        'application/x-sv4cpio'=>'sv4cpio',
        'application/x-sv4crc'=>'sv4crc',
        'application/x-tar'=>'tar',
        'application/x-tcl'=>'tcl',
        'application/x-tex'=>'tex',
        'application/x-texinfo'=>'texi',
        'application/x-troff'=>'roff',
        'application/x-troff-man'=>'man',
        'application/x-troff-me'=>'me',
        'application/x-troff-ms'=>'ms',
        'application/x-ustar'=>'ustar',
        'application/x-wais-source'=>'src',
        'application/xml'=>'xml',
        'application/zip'=>'zip',
        'audio/TSP-audio'=>'tsi',
        'audio/basic'=>'au',
        'audio/midi'=>'midi',
        'audio/mpeg'=>'mp3',
        'audio/x-aiff'=>'aif',
        'audio/x-pn-realaudio'=>'rm',
        'audio/x-pn-realaudio-plugin'=>'rpm',
        'audio/x-realaudio'=>'ra',
        'audio/x-wav'=>'wav',
        'chemical/x-pdb'=>'pdb',
        'image/bmp'=>'bmp',
        'image/cmu-raster'=>'ras',
        'image/gif'=>'gif',
        'image/ief'=>'ief',
        'image/jpeg'=>'jpg',
        'image/pjpeg'=>'jpg',
        'image/png'=>'png',
        'image/x-png'=>'png',
        'image/tiff'=>'tif',
        'image/x-portable-anymap'=>'pnm',
        'image/x-portable-bitmap'=>'pbm',
        'image/x-portable-graymap'=>'pgm',
        'image/x-portable-pixmap'=>'ppm',
        'image/x-rgb'=>'rgb',
        'image/x-xbitmap'=>'xbm',
        'image/x-xpixmap'=>'xpm',
        'image/x-xwindowdump'=>'xwd',
        'model/iges'=>'iges',
        'model/mesh'=>'mesh',
        'model/vrml'=>'vrml',
        'text/css'=>'css',
        'text/html'=>'html',
        'text/plain'=>'txt',
        'text/richtext'=>'rtx',
        'text/rtf'=>'rtf',
        'text/sgml'=>'sgml',
        'text/tab-separated-values'=>'tsv',
        'text/x-setext'=>'etx',
        'video/mpeg'=>'mpg',
        'video/quicktime'=>'mov',
        'video/vnd.vivo'=>'vivo',
        'video/x-fli'=>'fli',
        'video/x-flv'=>'flv',
        'video/x-msvideo'=>'avi',
        'video/x-sgi-movie'=>'movie',
        'www/mime'=>'mime',
        'x-conference/x-cooltalk'=>'ice',
    );

    static $exts = array(
        'ai'=>'application/postscript',
        'aif'=>'audio/x-aiff',
        'aifc'=>'audio/x-aiff',
        'aiff'=>'audio/x-aiff',
        'asc'=>'text/plain',
        'au'=>'audio/basic',
        'avi'=>'video/x-msvideo',
        'bcpio'=>'application/x-bcpio',
        'bin'=>'application/octet-stream',
        'bmp'=>'image/bmp',
        'c'=>'text/plain',
        'cc'=>'text/plain',
        'ccad'=>'application/clariscad',
        'cdf'=>'application/x-netcdf',
        'class'=>'application/octet-stream',
        'cpio'=>'application/x-cpio',
        'cpt'=>'application/mac-compactpro',
        'csh'=>'application/x-csh',
        'css'=>'text/css',
        'dcr'=>'application/x-director',
        'dir'=>'application/x-director',
        'dms'=>'application/octet-stream',
        'doc'=>'application/msword',
        'drw'=>'application/drafting',
        'dvi'=>'application/x-dvi',
        'dwg'=>'application/acad',
        'dxf'=>'application/dxf',
        'dxr'=>'application/x-director',
        'eps'=>'application/postscript',
        'etx'=>'text/x-setext',
        'exe'=>'application/octet-stream',
        'ez'=>'application/andrew-inset',
        'f'=>'text/plain',
        'f90'=>'text/plain',
        'fli'=>'video/x-fli',
        'flv'=>'video/x-flv',
        'gif'=>'image/gif',
        'gtar'=>'application/x-gtar',
        'gz'=>'application/x-gzip',
        'h'=>'text/plain',
        'hdf'=>'application/x-hdf',
        'hh'=>'text/plain',
        'hqx'=>'application/mac-binhex40',
        'htm'=>'text/html',
        'html'=>'text/html',
        'ice'=>'x-conference/x-cooltalk',
        'ief'=>'image/ief',
        'iges'=>'model/iges',
        'igs'=>'model/iges',
        'ips'=>'application/x-ipscript',
        'ipx'=>'application/x-ipix',
        'jpe'=>'image/jpeg',
        'jpeg'=>'image/jpeg',
        'jpg'=>'image/jpeg',
        'js'=>'application/x-javascript',
        'kar'=>'audio/midi',
        'latex'=>'application/x-latex',
        'lha'=>'application/octet-stream',
        'lsp'=>'application/x-lisp',
        'lzh'=>'application/octet-stream',
        'm'=>'text/plain',
        'man'=>'application/x-troff-man',
        'me'=>'application/x-troff-me',
        'mesh'=>'model/mesh',
        'mid'=>'audio/midi',
        'midi'=>'audio/midi',
        'mif'=>'application/vnd.mif',
        'mime'=>'www/mime',
        'mov'=>'video/quicktime',
        'movie'=>'video/x-sgi-movie',
        'mp2'=>'audio/mpeg',
        'mp3'=>'audio/mpeg',
        'mpe'=>'video/mpeg',
        'mpeg'=>'video/mpeg',
        'mpg'=>'video/mpeg',
        'mpga'=>'audio/mpeg',
        'ms'=>'application/x-troff-ms',
        'msh'=>'model/mesh',
        'nc'=>'application/x-netcdf',
        'oda'=>'application/oda',
        'pbm'=>'image/x-portable-bitmap',
        'pdb'=>'chemical/x-pdb',
        'pdf'=>'application/pdf',
        'pgm'=>'image/x-portable-graymap',
        'pgn'=>'application/x-chess-pgn',
        'png'=>'image/png',
        'pnm'=>'image/x-portable-anymap',
        'pot'=>'application/mspowerpoint',
        'ppm'=>'image/x-portable-pixmap',
        'pps'=>'application/mspowerpoint',
        'ppt'=>'application/mspowerpoint',
        'ppz'=>'application/mspowerpoint',
        'pre'=>'application/x-freelance',
        'prt'=>'application/pro_eng',
        'ps'=>'application/postscript',
        'qt'=>'video/quicktime',
        'ra'=>'audio/x-realaudio',
        'ram'=>'audio/x-pn-realaudio',
        'ras'=>'image/cmu-raster',
        'rgb'=>'image/x-rgb',
        'rm'=>'audio/x-pn-realaudio',
        'roff'=>'application/x-troff',
        'rpm'=>'audio/x-pn-realaudio-plugin',
        'rtf'=>'text/rtf',
        'rtx'=>'text/richtext',
        'scm'=>'application/x-lotusscreencam',
        'set'=>'application/set',
        'sgm'=>'text/sgml',
        'sgml'=>'text/sgml',
        'sh'=>'application/x-sh',
        'shar'=>'application/x-shar',
        'silo'=>'model/mesh',
        'sit'=>'application/x-stuffit',
        'skd'=>'application/x-koan',
        'skm'=>'application/x-koan',
        'skp'=>'application/x-koan',
        'skt'=>'application/x-koan',
        'smi'=>'application/smil',
        'smil'=>'application/smil',
        'snd'=>'audio/basic',
        'sol'=>'application/solids',
        'spl'=>'application/x-futuresplash',
        'src'=>'application/x-wais-source',
        'step'=>'application/STEP',
        'stl'=>'application/SLA',
        'stp'=>'application/STEP',
        'sv4cpio'=>'application/x-sv4cpio',
        'sv4crc'=>'application/x-sv4crc',
        'swf'=>'application/x-shockwave-flash',
        't'=>'application/x-troff',
        'tar'=>'application/x-tar',
        'tcl'=>'application/x-tcl',
        'tex'=>'application/x-tex',
        'texi'=>'application/x-texinfo',
        'texinfo'=>'application/x-texinfo',
        'tif'=>'image/tiff',
        'tiff'=>'image/tiff',
        'tr'=>'application/x-troff',
        'tsi'=>'audio/TSP-audio',
        'tsp'=>'application/dsptype',
        'tsv'=>'text/tab-separated-values',
        'txt'=>'text/plain',
        'unv'=>'application/i-deas',
        'ustar'=>'application/x-ustar',
        'vcd'=>'application/x-cdlink',
        'vda'=>'application/vda',
        'viv'=>'video/vnd.vivo',
        'vivo'=>'video/vnd.vivo',
        'vrml'=>'model/vrml',
        'wav'=>'audio/x-wav',
        'wrl'=>'model/vrml',
        'xbm'=>'image/x-xbitmap',
        'xlc'=>'application/vnd.ms-excel',
        'xll'=>'application/vnd.ms-excel',
        'xlm'=>'application/vnd.ms-excel',
        'xls'=>'application/vnd.ms-excel',
        'xlw'=>'application/vnd.ms-excel',
        'xml'=>'application/xml',
        'xpm'=>'image/x-xpixmap',
        'xwd'=>'image/x-xwindowdump',
        'xyz'=>'chemical/x-pdb',
        'zip'=>'application/zip',
    );

    static function mimeToExt($mime)
    {
        return isset(self::$mimes[$mime]) ? self::$mimes[$mime] : '';
    }

    static function extToMime($ext)
    {
        return isset(self::$exts[$ext]) ? self::$exts[$ext] : 'application/octet-stream';
    }
}