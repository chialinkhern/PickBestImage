/**
 * jsPsych plugin for norming images: participant is asked to pick the best one
 *
 * This plugin is a Frankenstein's monster of jspsych-vsl-grid-scene and jspsych-serial-reaction-time-mouse
 *
 * Lin Khern Chia
 *
 *
 */

jsPsych.plugins['clk-pick-best'] = (function() {

  var plugin = {};

  jsPsych.pluginAPI.registerPreload('vsl-grid-scene', 'stimuli', 'image');

  plugin.info = {
    name: 'clk-pick-best',
    description: '',
    parameters: {
      stimuli: {
        type: jsPsych.plugins.parameterType.IMAGE,
        pretty_name: 'Stimuli',
        array: true,
        default: undefined,
        description: 'An array that defines a grid.'
      },
      image_size: {
        type: jsPsych.plugins.parameterType.INT,
        pretty_name: 'Image size',
        array: true,
        default: [100,100],
        description: 'Array specifying the width and height of the images to show.'
      },
      prompt: {
        type: jsPsych.plugins.parameterType.STRING,
        pretty_name: "Prompt",
        array: true,
        default: null,
        description: "The prompt that accompanies the image grid."
      }
    }
  }

  plugin.trial = function(display_element, trial) {

    function track_mouse_down(display_element){ // adapted from jspsych-serial-reaction-time-mouse
      var response_grid = display_element.querySelectorAll("#jspsych-vsl-grid-scene-table-cell")
      // console.log(response_grid)
      for (var i=0; i<response_grid.length; i++){
        response_grid[i].addEventListener("mousedown", function(e){
          var resp_data = {}
          resp_data.row = e.currentTarget.getAttribute("data-row")
          resp_data.column = e.currentTarget.getAttribute("data-column")
          resp_data.rt = Date.now() - startTime
          after_response(resp_data)
        })
      }
    }

    function after_response(resp_data){
      response = resp_data //TODO: idk what to do here
      end_trial()
    }

    function end_trial(){
      // kill any remaining setTimeout handlers
      jsPsych.pluginAPI.clearAllTimeouts();

      var trial_data = {
        "rt": response.rt,
        "image_picked": trial.stimuli[response.row][response.column],
      }

      // clear the display
      display_element.innerHTML = "";

      // move on to next trial
      jsPsych.finishTrial(trial_data)
    }

    if (trial.prompt !== null){
      display_element.innerHTML = "\n"+trial.prompt
      display_element.innerHTML += plugin.generate_stimulus(trial.stimuli, trial.image_size);
      }
    else {display_element.innerHTML = plugin.generate_stimulus(trial.stimuli, trial.image_size);
}

    startTime = Date.now()
    track_mouse_down(display_element)
  };

  plugin.generate_stimulus = function(pattern, image_size) {
    var nrows = pattern.length;
    var ncols = pattern[0].length;

    // create blank element to hold code that we generate
    var html = '<div id="jspsych-vsl-grid-scene-dummy" css="display: none;">';

    // create table
    html += '<table id="jspsych-vsl-grid-scene table" '+
      'style="border-collapse: collapse; margin-left: auto; margin-right: auto;">';

    for (var row = 0; row < nrows; row++) {
      html += '<tr id="jspsych-vsl-grid-scene-table-row-'+row+'" css="height: '+image_size[1]+'px;">';

      for (var col = 0; col < ncols; col++) {
        html += '<td id="jspsych-vsl-grid-scene-table-' + row + '-' + col +'" '+
          'style="padding: '+ (image_size[1] / 10) + 'px ' + (image_size[0] / 10) + 'px;">'+
          '<div id="jspsych-vsl-grid-scene-table-cell"' + ' data-row=' + row + ' data-column=' + col + ' style="width: '+image_size[0]+'px; height: '+image_size[1]+'px;">';
        if (pattern[row][col] !== 0) {
          html += '<img '+
            'src="'+pattern[row][col]+'" style="width: '+image_size[0]+'px; height: '+image_size[1]+'"></img>';
        }
        html += '</div>';
        html += '</td>';
      }
      html += '</tr>';
    }

    html += '</table>';
    html += '</div>';

    html += '</div>'+''

    return html;

  };

  return plugin;
})();
