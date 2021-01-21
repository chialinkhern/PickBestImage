<!DOCTYPE html>
<html lang="en">
<head>
    <script src="jspsych-6.0.1/jspsych.js"></script>
    <script src="jspsych-6.0.1/plugins/clk-pick-best-image.js"></script>
    <script src="jspsych-6.0.1/plugins/jspsych-instructions.js"></script>
    <script src="jspsych-6.0.1/plugins/jspsych-call-function.js"></script>
    <script src="jspsych-6.0.1/plugins/jspsych-vsl-grid-scene.js"></script>
    <script src="helper_functions.js"></script>
    <script src="jspsych-6.0.1/plugins/jspsych-serial-reaction-time-mouse.js"></script>
    <link href="jspsych-6.0.1/css/jspsych.css" rel="stylesheet" type="text/css"></link>
</head>
<body>
</body>

<?php
	$dir = 'images';
	$dirs = scandir($dir, 1);
	$dirs = array_slice($dirs, 0, count($dirs)-2);
	$images = array();
	for ($i = 0; $i <= count($dirs); $i++){
	    $img_dir = 'images/' . $dirs[$i];
	    $dir_images = scandir($img_dir, 1);
	    $images[$dirs[$i]] = array_slice($dir_images, 0, count($dir_images)-2);
	};
?>

<script>
	let obj_names = <?php echo json_encode($dirs, JSON_HEX_TAG); ?>;
	let images = <?php echo json_encode($images, JSON_HEX_TAG); ?>;
	obj_names = shuffle(obj_names)
    let sub_num = Date.now()
//     let out_data = {obj_names: [], selected_imgs: [], : rts:[]}


    var pickbest = {
        type: 'clk-pick-best',
        stimuli: [[0],[0]],
        image_size: [350,350],
        on_start: function(){
            let obj_name = obj_names[iterate_objs.obj_num]
            let obj_images = images[obj_name]
            console.log(obj_images)
            for (i in obj_images){
                obj_images[i] = "images/"+obj_name+"/"+obj_images[i]
            }
            this.stimuli = listToMatrix(obj_images, 2)
        },
        on_finish: function(){

        }
    }

    let iterate_objs = {
      timeline: [pickbest],
      obj_num: 0,
      loop_function: function(){
	      iterate_objs.obj_num = iterate_objs.obj_num + 1
//     	      console.log(out_data)
	      if (iterate_objs.obj_num === obj_names.length){
	          return false
	      }
	      else {return true}
      }
    }



    timeline = [iterate_objs]
    jsPsych.init({
        timeline: timeline,
        show_preload_progress_bar: true,
    })
</script>

