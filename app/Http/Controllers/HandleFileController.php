<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HandleFile;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;

class HandleFileController extends Controller
{
    public function index()
    {
        // Get data from bd and show
        $data = HandleFile::get();
        return view('welcome',["data" => $data]);
    }


    public function processFile(Request $request)
    {
        // Write code for validation
        $request->validate([
            'uploadFileName' => 'required|mimes:pdf,docx|max:1024',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Declear variable for get extension

            // check file and upload on file directory
            if ($request->hasfile('uploadFileName')) {
                $file = $request->uploadFileName;

                if ($file->getClientOriginalExtension() == 'pdf') {
                    $input['content'] = $this->readPdfFile($file);
                }
                if ($file->getClientOriginalExtension() == 'docx') {
                    $content = $this->readDocxFile($file);
                    $input['content'] =$content;
                }

                

                $fileName = date('dmyhisa') . '-' . $file->getClientOriginalName();
                $fileName = str_replace(" ", "-", $fileName);
                $destinationPath = public_path('/uploads');
                
                if (!is_dir($destinationPath)) {
                    mkdir($destinationPath, 0777, TRUE);
                    @chmod($destinationPath,0777);
                }
                $file->move($destinationPath, $fileName);
                
                $extension = $file->getClientOriginalExtension();                

                // create aaray to store in DB
                $input['filename'] = 'uploads/'.$fileName;
                $input['extension'] = $file->getClientOriginalExtension();
                $input['orig_filename'] =  $file->getClientOriginalName();
            }
            
            // store data in to db            
            HandleFile::create($input);
            DB::commit();
            return redirect()->back()->with('success', 'File processed and data saved.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error',  $th->getMessage());
        }
    }

    //function for extract pdf
    private function readPdfFile($file)
    {
        // pdf-to-text to extract text from PDF
        $pdfParser = new Parser();
        $pdf = $pdfParser->parseFile($file->path());
        return $pdf->getText();
    }


    // function for extract docx
    private function readDocxFile($file)
    {
        $content = "";
        $zip = zip_open($file);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            zip_entry_close($zip_entry);
        } // end while

        zip_close($zip);
        $xml = simplexml_load_string($content);

        // Convert text into simle string
        $text = '';
        foreach ($xml->xpath('//w:t') as $t) {
            $text .= (string)$t;
        }
        return $text;
    }


    // edit file content 
    public function edit($id)
    {
        $edit = HandleFile::find(decrypt($id));
        $data = HandleFile::get();
        return view('welcome',["data" =>$data,"edit" => $edit]);
    }

    // update content 
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);

        try {
            DB::beginTransaction();            
            // update data in to db            
            HandleFile::where('id',decrypt($id))->update(['content' => $request->content]);
            DB::commit();
            return redirect()->route('welcome')->with('success', 'Content updated successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error',  $th->getMessage());
        }
        
    }

}
