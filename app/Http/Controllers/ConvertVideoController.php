<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ConvertVideoController extends Controller
{
    protected $exe;

    public function __construct()
    {
        $this->exe = env('FFMPEG_PATH');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd(1);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Ffmpeg = $this->exe . '\ffmpeg.exe';
        $Data = $request->input();
        $status = 200;
        try {
            if ($Data) {
                foreach ($Data as $item) {
                    $input_path = base_path('storage\app\public\input' . '\\' . $item['videoId']);
                    $output_path = base_path('storage\app\public\output' . '\\' . $item['start'] . '_' . $item['duration'] . '.mp4');
                    if (File::exists($input_path)) {

                    } else {

                    }
                }
                $response['Convert'] = 'Convert Success';
            } else {
                $response['error'] = 'ERROR';
                $status = 500;
            }
        } catch (\Exception $e) {
            $response['error'] = 'ERROR';
            $status = 500;
        } finally {
            return response()->json(
                [$response], $status);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
