const rowsPerPage = 10;
let currentPage = 1;
let sortColumn = 'rank';
let sortDir = 'desc';

let shotsChart, killsChart, roundsChart;

function openModal(player){
    const overlay = document.getElementById('playerModalOverlay');
    const nameEl = document.getElementById('modalName');
    const statsEl = document.getElementById('modalStats');

    const kd = player.deaths > 0 ? (player.kills / player.deaths) : player.kills;
    const accuracy = player.shoots > 0 ? (player.hits / player.shoots) * 100 : 0;
    const hsPercent = player.kills > 0 ? (player.headshots / player.kills) * 100 : 0;
    const totalRounds = player.round_win + player.round_lose;
    const hoursPlayed = player.playtime / 3600;

    nameEl.textContent = player.name;
    statsEl.innerHTML = `
        <span class="badge">Rating: ${player.value}</span>
        <span class="badge">Kills: ${player.kills}</span>
        <span class="badge">Deaths: ${player.deaths}</span>
        <span class="badge">K/D: ${kd.toFixed(2)}</span>
        <span class="badge">Assists: ${player.assists}</span>
        <span class="badge">Accuracy: ${accuracy.toFixed(2)}%</span>
        <span class="badge">Headshots: ${player.headshots} (${hsPercent.toFixed(2)}%)</span>
        <span class="badge">Rounds Won: ${player.round_win}</span>
        <span class="badge">Rounds Lost: ${player.round_lose}</span>
        <span class="badge">Total Rounds: ${totalRounds}</span>
        <span class="badge">Hours Played: ${hoursPlayed.toFixed(1)}</span>`;

    if(shotsChart) shotsChart.destroy();
    if(killsChart) killsChart.destroy();
    if(roundsChart) roundsChart.destroy();

    shotsChart = new Chart(document.getElementById('shotsChart'), {
        type: 'pie',
        data: {
            labels: ['Fired','Hit','Headshots'],
            datasets: [{
                data: [player.shoots, player.hits, player.headshots],
                backgroundColor: ['#4f75ff','#28a745','#ffc107']
            }]
        },
        options: {plugins:{legend:{position:'bottom'}}}
    });

    killsChart = new Chart(document.getElementById('killsChart'), {
        type: 'pie',
        data: {
            labels: ['Kills','Deaths'],
            datasets: [{
                data: [player.kills, player.deaths],
                backgroundColor: ['#17a2b8','#dc3545']
            }]
        },
        options: {plugins:{legend:{position:'bottom'}}}
    });

    roundsChart = new Chart(document.getElementById('roundsChart'), {
        type: 'pie',
        data: {
            labels: ['Won','Lost'],
            datasets: [{
                data: [player.round_win, player.round_lose],
                backgroundColor: ['#28a745','#dc3545']
            }]
        },
        options: {plugins:{legend:{position:'bottom'}}}
    });

    overlay.classList.add('show');
}

function closeModal(){
    const overlay = document.getElementById('playerModalOverlay');
    overlay.classList.remove('show');
}
function escapeHtml(str){
    return str.replace(/[&<>"']/g,function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]);});
}

function renderTable(){
    const tbody = document.getElementById('rank-body');
    if(!tbody) return;
    const searchTerm = document.getElementById('search').value.toLowerCase();
    let filtered = players.filter(p => p.name.toLowerCase().includes(searchTerm));

    filtered.sort((a,b)=>{
        let valA = a[sortColumn];
        let valB = b[sortColumn];
        if(sortColumn === 'kd'){
            valA = a.deaths > 0 ? a.kills/a.deaths : a.kills;
            valB = b.deaths > 0 ? b.kills/b.deaths : b.kills;
        }
        if(valA < valB) return sortDir === 'asc' ? -1 : 1;
        if(valA > valB) return sortDir === 'asc' ? 1 : -1;
        return 0;
    });

    const pageCount = Math.ceil(filtered.length / rowsPerPage);
    if(currentPage > pageCount) currentPage = pageCount || 1;
    const start = (currentPage-1)*rowsPerPage;
    const pageData = filtered.slice(start, start+rowsPerPage);

    tbody.innerHTML = '';
    pageData.forEach(r => {
        const kd = r.deaths > 0 ? (r.kills / r.deaths) : r.kills;
        const tr = document.createElement('tr');
        tr.className = 'player-summary';
        tr.innerHTML = `<td>${escapeHtml(r.name)}</td>`+
            `<td>${r.value}</td>`+
            `<td><img class="rank-img" src="/src/ranks/${r.rank}.png" alt="rank"></td>`+
            `<td>${r.kills}</td>`+
            `<td>${r.deaths}</td>`+
            `<td>${kd.toFixed(2)}</td>`;
        tr.addEventListener('click', () => openModal(r));
        tbody.appendChild(tr);
    });

    document.getElementById('pageInfo').textContent = `${currentPage}/${pageCount || 1}`;
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === pageCount || pageCount===0;

}

window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search').addEventListener('input', () => {
        currentPage = 1;
        renderTable();
    });
    document.getElementById('prevPage').addEventListener('click', () => {
        if(currentPage > 1){
            currentPage--;
            renderTable();
        }
    });
    document.getElementById('nextPage').addEventListener('click', () => {
        currentPage++;
        renderTable();
    });
    document.querySelectorAll('th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = th.dataset.column;
            if(sortColumn === col){
                sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = col;
                sortDir = col === 'rank' ? 'desc' : 'asc';
            }
            renderTable();
        });
    });
    document.getElementById('modalClose').addEventListener('click', closeModal);
    document.getElementById('playerModalOverlay').addEventListener('click', e => {
        if(e.target.id === 'playerModalOverlay') closeModal();
    });
    renderTable();
});