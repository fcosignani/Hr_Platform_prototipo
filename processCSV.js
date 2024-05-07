
function processData(csvText) {
    const allTextLines = csvText.split(/\r\n|\n/);
    const headers = allTextLines[0].split(',');
    let results = [];
    let currentPerson = null;
    let currentID = null;

    for (let i = 1; i < allTextLines.length; i++) {
        const row = allTextLines[i].split(',');

        if (row.length === headers.length) {
            const nameCell = row[0].trim().toLowerCase();

            if (nameCell === 'nombre') {
                currentPerson = row[3]; // asumiendo que el nombre está en la cuarta columna
                currentID = null; // reiniciar ID hasta que se encuentre en una fila posterior
            } else if (currentPerson && !currentID && !isNaN(row[0].trim())) {
                currentID = row[0].trim(); // asumiendo que el ID sigue al nombre y es numérico
            } else if (currentPerson && currentID && row[2] && row[5]) {
                const exitTimeStr = row[5].trim();
                const dateStr = row[2].trim();
                const dayOfWeek = getDayOfWeek(dateStr);

                if (dayOfWeek && exitTimeStr) {
                    const exitTime = parseTime(exitTimeStr);
                    let extraMinutesBefore21 = 0;
                    let extraMinutesAfter21 = 0;

                    if (exitTime > parseTime("21:00")) {
                        extraMinutesBefore21 = (new Date('1970-01-01T21:00') - new Date(`1970-01-01T${startOfDayTime}`)) / 60000;
                        console.log(extraMinutesBefore21);
                        alert(extraMinutesBefore21);
                        extraMinutesAfter21 = (exitTime - new Date('1970-01-01T21:00')) / 60000;
                    } else if (exitTime > parseTime("14:30") && dayOfWeek.toLowerCase() === 'friday') {
                        extraMinutesBefore21 = (exitTime - new Date('1970-01-01T14:30')) / 60000;
                    }

                    results.push({
                        name: currentPerson,
                        id: currentID,
                        date: dateStr,
                        dayOfWeek: dayOfWeek,
                        exitTime: exitTimeStr,
                        extraMinutesBefore21: extraMinutesBefore21,
                        extraMinutesAfter21: extraMinutesAfter21
                    });
                }
            }
        }
    }

    return results;
}

function processAndDisplay() {
    const fileInput = document.getElementById('csvFile');
    const file = fileInput.files[0];
    if (!file) {
        alert("Please select a file first.");
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        const data = processData(text);
        displayData(data);
    };
    reader.readAsText(file);
}

function submitData() {
    const data = [];
    const rows = document.getElementById('resultsTable').rows;
    for (let i = 1; i < rows.length; i++) {
        let rowData = {};
        for (let j = 0; j < rows[i].cells.length; j++) {
            rowData[rows[0].cells[j].textContent] = rows[i].cells[j].textContent;
        }
        data.push(rowData);
    }
    fetch('processCSV.php', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {'Content-Type': 'application/json'}
    }).then(response => response.json())
      .then(response => alert('Data submitted successfully'))
      .catch(error => alert('Data submitted successfully'));
}

function displayData(data) {
    const table = document.getElementById('resultsTable').getElementsByTagName('tbody')[0];
    table.innerHTML = ''; // Clear previous entries
    data.forEach(row => {
        let newRow = table.insertRow();
        Object.values(row).forEach(value => {
            let newCell = newRow.insertCell();
            newCell.textContent = value;
        });
    });
}

function downloadData() {
    const data = JSON.stringify([...document.getElementById('resultsTable').rows].slice(1).map(row => {
        const rowData = {};
        [...row.cells].forEach((cell, index) => rowData[document.getElementById('resultsTable').rows[0].cells[index].textContent] = cell.textContent);
        return rowData;
    }));
    const blob = new Blob([data], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = "downloaded_data.json";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


function getDayOfWeek(dateStr) {
    const date = new Date(dateStr);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[date.getDay()];
}

function parseTime(timeStr) {
    const [hours, minutes] = timeStr.split(':');
    return new Date(`1970-01-01T${hours}:${minutes}`);
}

