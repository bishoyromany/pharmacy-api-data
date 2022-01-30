<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spiritix\Html2Pdf\Converter;
use Spiritix\Html2Pdf\Input\StringInput;
use Spiritix\Html2Pdf\Input\UrlInput;
use Spiritix\Html2Pdf\Output\DownloadOutput;

class HTMLToPDFController extends Controller
{
    public function convert(Request $request)
    {
        // $input = new StringInput();
        // $input->setHtml($request->html);

        $input = new UrlInput();
        $input->setUrl("https://www.youtube.com/watch?v=_CtWwFweeFk");

        $converter = new Converter($input, new DownloadOutput());

        $converter->setOption('landscape', true);

        $converter->setOptions([
            'printBackground' => true,
            'displayHeaderFooter' => false,
            'pageRanges' => 1,
            'format' => 'A1',
        ]);

        $output = $converter->convert();
        dd($output);
        // $output->download('google.pdf');
    }
}
