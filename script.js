function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.matches('.profile')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}

function showSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.style.display = 'flex';
}

function hideSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.style.display = 'none';
}





document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#add-set').forEach(button => {
        button.addEventListener('click', () => {
            const tbody = button.closest('form').querySelector('table tbody');

            // Create a new row for the set
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>Set ${tbody.children.length + 1}</td>        
                <td><input type="number" name="weight[]" step="0.1" required style="width: 60px;" class="auto-save"></td>
                <td><input type="number" name="reps[]" required style="width: 60px;" class="auto-save"></td>
                <td><input type="text" name="note[]" style="width: 200px;" class="auto-save"></td>
            `;

            tbody.appendChild(newRow);
        });
    });
});



document.addEventListener('DOMContentLoaded', () => {
    const autoSaveInputs = document.querySelectorAll('.auto-save');

    autoSaveInputs.forEach(input => {
        let saveTimeout;

        input.addEventListener('input', () => {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const setId = input.dataset.setId;
                const field = input.name;
                const value = input.value;

                // Prepare data to send
                const data = new FormData();
                data.append('set_id', setId);
                data.append('field', field);
                data.append('value', value);

                // Send data to the server
                fetch('update_set.php', {
                    method: 'POST',
                    body: data
                }).then(response => response.json())
                  .then(result => {
                      if (result.success) {
                          console.log('Set updated successfully.');
                      } else {
                          console.error('Error updating set:', result.error);
                      }
                  });
            }, 200);
        });
    });
});

