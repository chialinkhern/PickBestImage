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
    let out_data = {obj_names: [], images_picked: [], rts:[]}

    var pickbest = {
        type: 'clk-pick-best',
        obj_name: "",
        stimuli: [[0],[0]],
        prompt: null,
        image_size: [375,375],
        on_start: function(){
            this.obj_name = obj_names[iterate_objs.obj_num]
            this.prompt = "Click on the best "+"<b>"+this.obj_name+"</b>!"
            let obj_images = images[this.obj_name]
            for (i in obj_images){
                obj_images[i] = "images/"+this.obj_name+"/"+obj_images[i]
            }
            this.stimuli = listToMatrix(obj_images, 2)
            console.log(this.stimuli)
        },
        on_finish: function(trial_data){
            out_data.obj_names.push(this.obj_name)
            out_data.images_picked.push(trial_data.image_picked)
            out_data.rts.push(trial_data.rt)
        }
    }

    let iterate_objs = {
      timeline: [pickbest],
      obj_num: 0,
      loop_function: function(){
	      iterate_objs.obj_num = iterate_objs.obj_num + 1
	      if (iterate_objs.obj_num === obj_names.length){
	          return false
	      }
	      else {return true}
        }
    }

    let write_data = {
        type:"call-function",
        func: function(){
            let name = "subj_" + String(sub_num)
            let data = prep_data(out_data)
            save_data(name, data)
            console.log(out_data)
        }
    }

    let end_instructions = {
        type: "instructions",
        pages: ["That's all! Thank you for your time."],
        show_clickable_nav: false
    }

    timeline = [iterate_objs, write_data, end_instructions]
    jsPsych.init({
        timeline: timeline,
        show_preload_progress_bar: true,
    })
</script>

