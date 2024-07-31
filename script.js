document.addEventListener('DOMContentLoaded', () => {
    loadProjects();
    loadHours();
});

function addProject() {
    const projectName = document.getElementById('projectName').value;
    if (projectName) {
        fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=addProject&nombre=${projectName}`
        }).then(response => response.text()).then(data => {
            document.getElementById('projectName').value = '';
            loadProjects();
        });
    }
}

function loadProjects() {
    fetch('backend.php?action=getProjects')
        .then(response => response.json())
        .then(data => {
            const projectList = document.getElementById('projectList');
            const projectSelect = document.getElementById('projectSelect');
            projectList.innerHTML = '';
            projectSelect.innerHTML = '<option value="">Selecciona un proyecto</option>';
            data.forEach(project => {
                projectList.innerHTML += `<li>${project.nombre}</li>`;
                projectSelect.innerHTML += `<option value="${project.id}">${project.nombre}</option>`;
            });
        });
}

function addHours() {
    const projectSelect = document.getElementById('projectSelect').value;
    const hours = document.getElementById('hours').value;
    if (projectSelect && hours) {
        fetch('backend.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=addHours&proyecto_id=${projectSelect}&horas=${hours}`
        }).then(response => response.text()).then(data => {
            document.getElementById('hours').value = '';
            loadHours();
        });
    }
}

function loadHours() {
    const projectSelect = document.getElementById('projectSelect').value;
    if (projectSelect) {
        fetch(`backend.php?action=getHours&proyecto_id=${projectSelect}`)
            .then(response => response.json())
            .then(data => {
                const hoursTableBody = document.getElementById('hoursTableBody');
                hoursTableBody.innerHTML = `<tr><td>${document.getElementById('projectSelect').selectedOptions[0].text}</td><td>${data.horas}</td></tr>`;
            });
    } else {
        document.getElementById('hoursTableBody').innerHTML = '';
    }
}
