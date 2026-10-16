function searchTrees() {

    const input = document.getElementById('buscar').value;

    fetch('api/arboles.php?buscar=' + encodeURIComponent(input))
        .then(response => response.json())
        .then(arboles => {

            const rows = document.querySelectorAll('table tr:not(:first-child)');

            rows.forEach(row => {

                const id = row.cells[0].textContent;

                const encontrado = arboles.some(arbol => arbol.id == id);

                row.style.display = encontrado ? '' : 'none';
            });

        })
        .catch(error => {
            console.error('Error al consultar la API:', error);
        });
}
