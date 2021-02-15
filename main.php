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
    <script src="jspsych-6.0.1/plugins/jspsych-survey-html-form.js"></script>
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
    let out_data = {obj_names: [], images_picked: [], rts:[], eng_first: [], eng_learned: [], subnum: []}
    lang_survey_dat = null

    var pickbest = {
        type: 'clk-pick-best',
        obj_name: "",
        stimuli: [[0],[0]],
        prompt: null,
        image_size: [260,260],
        on_start: function(){
            this.obj_name = obj_names[iterate_objs.obj_num]
            this.prompt = "Click on the most representative "+"<b>"+this.obj_name+"</b>!"
            let obj_images = images[this.obj_name]
            for (i in obj_images){
                obj_images[i] = "images/"+this.obj_name+"/"+obj_images[i]
            }
            obj_images = shuffle(obj_images)
            this.stimuli = listToMatrix(obj_images, 2)
            console.log(this.stimuli)
        },
        on_finish: function(trial_data){
            out_data.obj_names.push(this.obj_name)
            out_data.images_picked.push(trial_data.image_picked)
            out_data.rts.push(trial_data.rt)
            out_data.eng_first.push(lang_survey_dat.yesno)
            out_data.eng_learned.push(lang_survey_dat.textbox)
            out_data.subnum.push(sub_num)
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

    let language_survey = {
        type: "survey-html-form",
        html: '<p> Is English your <b>first language</b>? <select name="yesno" id="yesno"> <option value="Yes"> Yes</option><option value="No"> No</option></select></p>'
        + '<p> In which country did you learn English? <input name="textbox" type="text" /></p>',
        button_label: "Begin Experiment",
        on_finish: function(trial_data){
            lang_survey_dat = JSON.parse(trial_data.responses)
        }
    }

    let introduction = {
        type: "instructions",
        pages: ["<p>Hello! Welcome to our experiment. You will be seeing a variety of images of different objects.</p> <p>Your job here is to click on, for each object, the <b>most representative image</b></p>", "<p>The <b>most representative image</b> of an object fits your expectation of what that object looks like the best.</p> <p>For example, maybe when told to think of a dog, a Golden Retriever comes to mind as what you'd expect to see. A Golden Retriever, to you, would be the most representative dog.</p><p>If none of the displayed images are at all representative of the object, click on the <b>These are all equally bad examples</b> button. Only choose this option if you really think all four images are not properly described by the label. If one or more are okay, pick the best one.</p> <p>We would like you to click on these images as quickly as possible- don't overthink your choice. We're interested in your first impressions.</p>",
         "<p>There are " + obj_names.length + " objects for you to decide on. It shouldn't take more than 10 minutes.</p> <p>Before you begin, we'd like you to answer two quick questions about your language background on the following page.</p>"],
         show_clickable_nav: true
    }

    timeline = [introduction, language_survey, iterate_objs, write_data, end_instructions]
    jsPsych.init({
        timeline: timeline,
        show_preload_progress_bar: true,
    })
</script>

