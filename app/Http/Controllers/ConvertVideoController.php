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

    public function convertMultiVideo(Request $request)
    {
        set_time_limit(0);
        $Ffmpeg = $this->exe . '\ffmpeg.exe';
        $Data = $request->input();
        $status = 200;
        try {
            if ($Data) {
                $cut_input_txt = 'cut_input.txt';
                $cut_input_file = fopen($cut_input_txt, 'w');
                $folder_output = 'output';
                if (!file_exists(base_path('storage\app\public\\' . $folder_output))) {
                    mkdir(base_path('storage\app\public\\' . $folder_output), 0777, true);
                }
                foreach ($Data as $item) {
                    $input_path = base_path('storage\app\public\input' . '\\' . $item['videoId']);
                    $array = explode('.', $item['videoId']);
                    $output_path = base_path('storage\app\public\\' . $folder_output . '\\' . $array[0] . '_' . $item['start'] . '_' . $item['duration'] . '.mp4');
                    if (File::exists($input_path)) {
                        $Command = $Ffmpeg . ' -i ' . $input_path . ' -ss ' . $item['start'] . ' -t ' . ($item['duration'] + 0.1) . ' -vf "scale=1920:1080" -r 25 -c:v libx264 -preset fast -c:a aac -y ' . $output_path;
                        shell_exec($Command);
                        fwrite($cut_input_file, "file '$output_path'" . PHP_EOL);
                    }
                }
                $folder_output_concat = 'output_concat';
                if (!file_exists(base_path('storage\app\public\\' . $folder_output_concat))) {
                    mkdir(base_path('storage\app\public\\' . $folder_output_concat), 0777, true);
                }
                $output_video_path = base_path('storage\app\public\\' . $folder_output_concat . '\\' . 'output_video.mp4');
                $Commands = $Ffmpeg . ' -f concat -safe 0 -i ' . $cut_input_txt . ' -c:v copy -c:a copy -y ' . $output_video_path;
                shell_exec($Commands);
                unlink($cut_input_txt);
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

    public function zoomVideo(Request $request)
    {
        set_time_limit(0);
        $Ffmpeg = $this->exe . '\ffmpeg.exe';
        $Data = $request->input();
        $status = 200;
        try {
            if ($Data) {
                $zoom_input_txt = 'zoom_input.txt';
                $zoom_input_file = fopen($zoom_input_txt, 'w');
                $folder_output = 'output-video-zoom';
                if (!file_exists(base_path('storage\app\public\\' . $folder_output))) {
                    mkdir(base_path('storage\app\public\\' . $folder_output), 0777, true);
                }
                foreach ($Data as $item) {
                    $input_path = base_path('storage\app\public\input' . '\\' . $item['videoId']);
                    $array = explode('.', $item['videoId']);
                    $output_path = base_path('storage\app\public\\' . $folder_output . '\\' . $array[0] . '_' . $item['x-zoom'] . '_' . $item['y-zoom'] . '.mp4');
                    if (File::exists($input_path)) {
                        $duration_zoom = 1000000;
                        if ($item['scale-zoom-before'] || $item['scale-zoom-after']) {
                            $Command = $Ffmpeg . " -i " . $input_path . " -vf \"zoompan=z='if(between(in_time,0,1),min(max(zoom,pzoom)+100," . $item['scale-zoom-before'] . "),if(between(in_time,1," . $duration_zoom . "),max(max(zoom,pzoom)-" . $item['speed-zoom']
                                . "," . $item['scale-zoom-after'] . ")))':d=1:x='if(lt(in_time,1)," . $item['x-zoom'] . ",if(lt(in_time,2),(" . $item['x-zoom'] . "+(" . $item['x-zoom-after'] . "-" . $item['x-zoom'] . ")*(in_time-1))," . $item['x-zoom-after'] . "))'" .
                                ":y='if(lt(in_time,1)," . $item['y-zoom'] . ",if(lt(in_time,2),(" . $item['y-zoom'] . "+(" . $item['y-zoom-after'] . "-" . $item['y-zoom'] . ")*(in_time-1))," . $item['y-zoom-after'] . "))'"
                                . ":s='1920x1080',rotate='if(between(t,0,1)," . $item['rotate-before'] . "*PI/180,if(between(t,1,2),(t-1)*(" .
                                $item['rotate-after'] ."*PI/180-" . $item['rotate-before'] . "*PI/180)/(2-1)+" . $item['rotate-before'] . "*PI/180," . $item['rotate-after'] .
                                "*PI/180))'\"  -c:v libx264 -crf 20 -preset fast -c:a copy -y " . $output_path;
                            shell_exec($Command);
                        } else {
                            $Command = $Ffmpeg . " -i " . $input_path . " -vf zoompan=z='if(between(in_time," . $item['start-zoom'] . "," . $duration_zoom . "),min(max(zoom,pzoom)+" . $item['speed-zoom']
                                . "," . $item['scale-zoom'] . "))':d=1:x='" . $item['x-zoom'] . "':y='" . $item['y-zoom'] . "':s='1920x1080' -y " . $output_path;
                            shell_exec($Command);
                        }
                        fwrite($zoom_input_file, "file '$output_path'" . PHP_EOL);
                    }
                }
                $folder_output_concat = 'output_concat_zoom';
                if (!file_exists(base_path('storage\app\public\\' . $folder_output_concat))) {
                    mkdir(base_path('storage\app\public\\' . $folder_output_concat), 0777, true);
                }
                $output_video_path = base_path('storage\app\public\\' . $folder_output_concat . '\\' . 'output_video_zoom.mp4');
                $Commands = $Ffmpeg . ' -f concat -safe 0 -i ' . $zoom_input_txt . ' -c:v copy -c:a copy -y ' . $output_video_path;
                shell_exec($Commands);
                unlink($zoom_input_txt);
                $response['Zoom'] = 'Zoom Success';
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
}
