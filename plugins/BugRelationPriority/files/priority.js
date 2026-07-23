document.addEventListener('DOMContentLoaded', function() {
    var dataDiv = document.getElementById('bug-relation-priorities-data');
    if (!dataDiv) return;

    var priorityDataStr = dataDiv.getAttribute('data-priorities');
    if (!priorityDataStr) return;

    var priorityData;
    try {
        priorityData = JSON.parse(priorityDataStr);
    } catch (e) {
        return;
    }

    var rows = document.querySelectorAll('table tbody tr');

    rows.forEach(function(tr) {
        if (tr.cells.length >= 5) {
            var cell0 = tr.cells[0];
            var cell1 = tr.cells[1];

            if (cell0.querySelector('span.nowrap') && cell1.querySelector('a[href*="view.php?id="]')) {
                var link = cell1.querySelector('a');
                var match = link.href.match(/view\.php\?id=(\d+)/);

                if (match && match[1]) {
                    var bugId = match[1];
                    var priorityText = priorityData[bugId] ? priorityData[bugId] : 'N/D';

                    var newTd = document.createElement('td');
                    var newSpan = document.createElement('span');
                    newSpan.textContent = priorityText;
                    newTd.appendChild(newSpan);
                    newTd.style.verticalAlign = 'middle';

                    var lastCell = tr.cells[tr.cells.length - 1];
                    tr.insertBefore(newTd, lastCell);
                }
            }
        }
    });
});
