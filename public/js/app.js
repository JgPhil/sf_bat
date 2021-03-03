
let count = 1;
function nextTurn() {
    fetch('/next-turn').then(
        response => response.json())
        .then(json => {
            if (!json.winner) {
                updateContent(json);
            } else {
                if (count < 2) {
                    updateContent(json);
                }
                count++;
                let fightBtn = document.querySelector("#fight");
                fightBtn.removeAttribute('onclick');
                fightBtn.setAttribute('value', 'Fini !');
                fightBtn.style.opacity = 0.3;
                document.querySelector("#refresh").removeAttribute("hidden");
            }
        })
        .catch(error => console.log(error));
}

function actualize() {
    location.reload();
}

function updateContent(json) {
    updateTable(json);
    addSummaryRow(json.summary);
}

function updateTable(json) {
    let winner = json.winner == true ? 'Gagnant: ' : '';
    let classTd = json.winner == true ? ' style="color:green";': '';
    let statusRow = document.querySelector("tbody");
    let html = `
                <tr>
                    <th>Nom</th>
                    <th>Image</th>
                    <th>PV</th>
                    <th>Poison</th>
                </tr>                
                `;
    statusRow.innerHTML = "";
    json.status.forEach(element => {
        let plague = element.name == 'Witch' ? '/' : (element.plague ? 'yes' : 'no');
        html += `
                <tr>
                    <td${classTd}>${winner + element.name}</td>
                    <td><img src="img/${element.name}.png" alt="${element.name}"></td>
                    <td>${element.health}</td>
                    <td>${plague}</td>
                </tr>
                ` ;
    }
    );
    statusRow.innerHTML = html;
}

function addSummaryRow(str) {
    let summaryRow = document.querySelector("#summary")
    summaryRow.innerText += str;

}
