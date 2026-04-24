document.addEventListener('DOMContentLoaded', function() {
    // 1. Buscamos el elemento HTML oculto que PHP generó
    var dataDiv = document.getElementById('bug-relation-priorities-data');
    if (!dataDiv) return;

    // 2. Extraemos los datos de las prioridades (JSON)
    var priorityDataStr = dataDiv.getAttribute('data-priorities');
    if (!priorityDataStr) return;
    
    var priorityData = JSON.parse(priorityDataStr);

    // 3. Ejecutamos la inyección en la tabla
    var rows = document.querySelectorAll('table tbody tr');
    
    rows.forEach(function(tr) {
        if (tr.cells.length >= 5) { 
            var cell0 = tr.cells[0];
            var cell1 = tr.cells[1];
            
            // Validamos que sea la fila de la tabla de relaciones
            if (cell0.querySelector('span.nowrap') && cell1.querySelector('a[href*="view.php?id="]')) {
                var link = cell1.querySelector('a');
                var match = link.href.match(/view\.php\?id=(\d+)/);
                
                if (match && match[1]) {
                    var bugId = match[1];
                    var priorityText = priorityData[bugId] ? priorityData[bugId] : 'N/D';
                    
                    var newTd = document.createElement('td');
                    newTd.innerHTML = '<span>' + priorityText + '</span>';
                    newTd.style.verticalAlign = 'middle';
                    
                    // Insertamos justo antes de la última celda
                    var lastCell = tr.cells[tr.cells.length - 1];
                    tr.insertBefore(newTd, lastCell);
                }
            }
        }
    });
});