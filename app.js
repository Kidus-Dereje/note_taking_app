const API_URL = './backend/api.php';

function fetchNotes() {
    fetch(API_URL)
        .then(res => res.json())
        .then(notes => {
            const notesList = document.getElementById('notesList');
            notesList.innerHTML = '';
            notes.forEach(note => {
                const li = document.createElement('li');
                li.textContent = note.text;
                const delBtn = document.createElement('button');
                delBtn.textContent = 'Delete';
                delBtn.className = 'delete-btn';
                delBtn.onclick = () => deleteNote(note.id);
                li.appendChild(delBtn);
                notesList.appendChild(li);
            });
        });
}

function addNote(text) {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text })
    })
    .then(res => res.json())
    .then(() => {
        document.getElementById('noteText').value = '';
        fetchNotes();
    });
}

function deleteNote(id) {
    fetch(API_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(() => fetchNotes());
}

document.getElementById('noteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const text = document.getElementById('noteText').value.trim();
    if (text) addNote(text);
});

window.onload = fetchNotes;
