/**
 * BugRelationPriority - reconstruye la tabla de relaciones segun el plan que
 * entrega el PHP.
 *
 * El PHP manda la estructura REAL de la tabla de esa pagina (columnas del core
 * presentes, cuales se ocultan, que columnas extra y en que orden), porque la
 * anatomia de la tabla varia: la columna de proyecto solo existe cuando las
 * relaciones cruzan proyectos. Aqui no se adivina nada con indices.
 *
 * Si el plan no existe o algo no cuadra, el script se retira sin tocar nada:
 * la tabla queda exactamente como la pinto el core.
 */
(function () {
	'use strict';

	var WIDGET = '#relationships';
	var EMPTY = '-';

	function readPlan() {
		var node = document.getElementById('brp-plan');
		if (!node) {
			return null;
		}
		var raw = node.getAttribute('data-plan');
		if (!raw) {
			return null;
		}
		try {
			return JSON.parse(raw);
		} catch (e) {
			return null;
		}
	}

	/** Id del caso relacionado, tomado de la columna que el core marca como ID. */
	function relatedBugId(plan, row) {
		var idx = plan.core.indexOf('id');
		if (idx < 0 || !row.cells[idx]) {
			return null;
		}
		var link = row.cells[idx].querySelector('a[href*="id="]');
		if (!link) {
			return null;
		}
		var match = link.href.match(/[?&]id=(\d+)/);
		return match ? match[1] : null;
	}

	function makeCell(text) {
		var td = document.createElement('td');
		td.className = 'brp-extra-cell';
		var span = document.createElement('span');
		span.className = 'brp-extra-value';
		/* textContent, nunca innerHTML: los valores vienen de la BD. */
		span.textContent = text;
		td.appendChild(span);
		return td;
	}

	function makeHeaderRow(labels) {
		var tr = document.createElement('tr');
		labels.forEach(function (label) {
			var th = document.createElement('th');
			th.textContent = label;
			tr.appendChild(th);
		});
		return tr;
	}

	function apply(plan) {
		var table = document.querySelector(WIDGET + ' .widget-main table');
		if (!table || !table.tBodies.length) {
			return;
		}

		var body = table.tBodies[0];
		var hidden = plan.hidden || [];
		var extras = plan.extras || [];
		var values = plan.values || {};
		var expected = plan.core.length;

		/* Indices (en plan.core) de las columnas a eliminar, de mayor a menor
		 * para que los indices no se corran durante el borrado. */
		var dropIdx = [];
		plan.core.forEach(function (key, i) {
			if (hidden.indexOf(key) !== -1) {
				dropIdx.push(i);
			}
		});
		dropIdx.reverse();

		/* Columnas del core que quedan visibles, en orden. */
		var visibleCore = plan.core.filter(function (key) {
			return hidden.indexOf(key) === -1;
		});

		var rows = Array.prototype.slice.call(body.rows);
		rows.forEach(function (row) {
			/* La fila de aviso ("bloquea - no resuelto") tiene colspan y una
			 * sola celda: no es una fila de datos, se deja intacta. */
			if (row.cells.length !== expected) {
				return;
			}
			var bugId = relatedBugId(plan, row);
			if (!bugId) {
				return;
			}

			dropIdx.forEach(function (i) {
				if (row.cells[i]) {
					row.deleteCell(i);
				}
			});

			var bugValues = values[bugId] || {};

			/* Referencia estable: la celda del cliente, que es la ultima del
			 * core y contiene el boton de borrar. Insertar "antes de" esta
			 * celda mantiene el orden de las columnas y deja el boton al
			 * borde derecho. Si el administrador eligio al final, se anexa. */
			var anchor = plan.beforeLast ? row.cells[row.cells.length - 1] : null;

			extras.forEach(function (extra) {
				var text = bugValues[extra.key];
				var cell = makeCell(text === undefined || text === '' ? plan.empty || EMPTY : text);
				if (anchor) {
					row.insertBefore(cell, anchor);
				} else {
					row.appendChild(cell);
				}
			});
		});

		if (!plan.showHeader) {
			return;
		}

		/* Orden del encabezado = orden real del cuerpo. */
		var labels = visibleCore.map(function (key) {
			return plan.labels[key] || key;
		});
		var extraLabels = extras.map(function (extra) {
			return extra.label;
		});

		if (plan.beforeLast && visibleCore.length) {
			var summaryIdx = visibleCore.length - 1;
			labels = labels.slice(0, summaryIdx)
				.concat(extraLabels)
				.concat(labels.slice(summaryIdx));
		} else {
			labels = labels.concat(extraLabels);
		}

		if (table.tHead) {
			table.removeChild(table.tHead);
		}
		var thead = document.createElement('thead');
		thead.appendChild(makeHeaderRow(labels));
		table.insertBefore(thead, table.firstChild);
	}

	function init() {
		var plan = readPlan();
		if (!plan || !plan.core || !plan.core.length) {
			return;
		}
		apply(plan);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
