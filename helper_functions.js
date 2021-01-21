function shuffle(array) {
    var currentIndex = array.length, temporaryValue, randomIndex;

    // While there remain elements to shuffle...
    while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
    }
  return array;
}

function prep_data(data) { // Trisha's function
    var datacsv = "";
    var labels = Object.keys(data); //grabs all the properties of data

    for (n = 0; n < labels.length; n++){
        datacsv = datacsv + labels[n] + ',';
        }
    datacsv = datacsv + '\n';

    let ntoloop = data[Object.keys(data)[0]].length;
    for (n = 0; n < ntoloop; n++){
        for (var i in data){
            if (data.hasOwnProperty(i)){
                datacsv = datacsv + data[i][n] + ','; //in "str" + num, num is converted to a string.
                }
            }
        datacsv = datacsv + '\n';
        }
    return datacsv;
}

function save_data(name, data){
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'write_data.php'); // 'write_data.php' is the path to the php file
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.send(JSON.stringify({filename: name, filedata: data}));
}

function listToMatrix(list, elementsPerSubArray) { //https://stackoverflow.com/questions/4492385/how-to-convert-simple-array-into-two-dimensional-array-matrix-with-javascript
    var matrix = [], i, k;

    for (i = 0, k = -1; i < list.length; i++) {
        if (i % elementsPerSubArray === 0) {
            k++;
            matrix[k] = [];
        }

        matrix[k].push(list[i]);
    }

    return matrix;
}