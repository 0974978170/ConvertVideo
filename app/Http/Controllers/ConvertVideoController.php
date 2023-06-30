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
    public function convertVideo(Request $request)
    {
        set_time_limit(0);
        $Ffmpeg = $this->exe . '\ffmpeg.exe';
        $Data = $request->input();
        $status = 200;
        try {
            if ($Data) {
                foreach ($Data as $item) {
                    $input_path = base_path('storage\app\public\input' . '\\' . $item['videoId']);
                    $array = explode('.',$item['videoId']);
                    $output_path = base_path('storage\app\public\output' . '\\' . $array[0] . '_' . $item['start'] . '_' . $item['duration'] . '.mp4');
                    if (File::exists($input_path)) {
                        $Command = $Ffmpeg . ' -i ' . $input_path . ' -ss ' . $item['start'] . ' -t ' . $item['duration'] . ' -vf "scale=1920:1080" -c:v libx264 -preset fast -c:a pcm_s16le -f avi ' . $output_path;
                        shell_exec($Command);
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
    public function convertMultiVideo(Request $request)
    {
        set_time_limit(0);
        $Ffmpeg = $this->exe . '\ffmpeg.exe';
        $Data = $request->input();
        $status = 200;
        try {
            if ($Data) {
                $cut_input_txt = base_path('storage\app\public\input\cut_input.txt');
                $cut_input_file = fopen($cut_input_txt, 'w');
                foreach ($Data as $item) {
                    $input_path = base_path('storage\app\public\input' . '\\' . $item['videoId']);
                    $array = explode('.',$item['videoId']);
                    $output_path = base_path('storage\app\public\output' . '\\' . $array[0] . '_' . $item['start'] . '_' . $item['duration'] . '.mp4');
                    if (File::exists($input_path)) {
                        $Command = $Ffmpeg . ' -i ' . $input_path . ' -ss ' . $item['start'] . ' -t ' . $item['duration'] . ' -vf "scale=1920:1080" -c:v libx264 -preset fast -c:a pcm_s16le -f avi ' . $output_path;
                        shell_exec($Command);
                        fwrite($cut_input_file, "file '$output_path'" . PHP_EOL);
                    }
                }
                $output_video_path = base_path('storage\app\public\output' . '\\' . 'output_video.mp4');
                $Command = $Ffmpeg . '"' . ' -f concat -safe 0 -i ' . $cut_input_txt . ' -c:a pcm_s16le -f avi ' . '"' . $output_video_path;
                shell_exec($Command);
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
