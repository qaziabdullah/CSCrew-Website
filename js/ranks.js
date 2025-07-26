const rowsPerPage = 10;
let currentPage = 1;
let sortColumn = 'rank';
let sortDir = 'desc';

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
        const accuracy = r.shoots > 0 ? (r.hits / r.shoots) * 100 : 0;
        const hsPercent = r.kills > 0 ? (r.headshots / r.kills) * 100 : 0;
        const totalRounds = r.round_win + r.round_lose;
        const hoursPlayed = r.playtime / 3600;
        const trSum = document.createElement('tr');
        trSum.className = 'player-summary';
        trSum.innerHTML = `<td>${escapeHtml(r.name)}</td>`+
            `<td>${r.value}</td>`+
            `<td><img class="rank-img" src="/src/ranks/${r.rank}.png" alt="rank"></td>`+
            `<td>${r.kills}</td>`+
            `<td>${r.deaths}</td>`+
            `<td>${kd.toFixed(2)}</td>`;
        const trDet = document.createElement('tr');
        trDet.className = 'player-details';
        trDet.innerHTML = `<td colspan="6"><div class="details">`+
            `<span class="badge">Shots Fired: ${r.shoots}</span>`+
            `<span class="badge">Shots Hit: ${r.hits}</span>`+
            `<span class="badge">Accuracy: ${accuracy.toFixed(2)}%</span>`+
            `<span class="badge">Headshots: ${r.headshots} (${hsPercent.toFixed(2)}%)</span>`+
            `<span class="badge">Assists: ${r.assists}</span>`+
            `<span class="badge">Rounds Won: ${r.round_win}</span>`+
            `<span class="badge">Rounds Lost: ${r.round_lose}</span>`+
            `<span class="badge">Total Rounds: ${totalRounds}</span>`+
            `<span class="badge">Hours Played: ${hoursPlayed.toFixed(1)}</span>`+
            `</div></td>`;
        tbody.appendChild(trSum);
        tbody.appendChild(trDet);
    });

    document.getElementById('pageInfo').textContent = `${currentPage}/${pageCount || 1}`;
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === pageCount || pageCount===0;

    document.querySelectorAll('.player-summary').forEach(row => {
        row.addEventListener('click', () => {
            const next = row.nextElementSibling;
            if(next && next.classList.contains('player-details')){
                next.classList.toggle('open');
            }
        });
    });
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
    renderTable();
});