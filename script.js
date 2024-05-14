// Sample data
let lists = [];

// Load lists from local storage if available
if (localStorage.getItem('lists')) {
    lists = JSON.parse(localStorage.getItem('lists'));
}

// Function to render lists
function renderLists() {
    const listsContainer = document.getElementById('lists-container');
    listsContainer.innerHTML = '';

    lists.forEach(list => {
        const listElement = document.createElement('div');
        listElement.classList.add('list');

        const header = document.createElement('div');
        header.classList.add('list-header');
        const title = document.createElement('h2');
        title.textContent = list.name;
        header.appendChild(title);

        const options = document.createElement('div');
        options.classList.add('list-options');
        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Delete';
        deleteBtn.onclick = function() {
            showModal(list);
        };
        options.appendChild(deleteBtn);
        header.appendChild(options);

        listElement.appendChild(header);

        const itemsList = document.createElement('ul');
        list.items.forEach(item => {
            const itemElement = document.createElement('li');
            itemElement.innerHTML = `<input type="checkbox" ${item.checked ? 'checked' : ''} onclick="toggleItem(${list.idx}, ${item.idx})">${item.text}`;
            itemsList.appendChild(itemElement);
        });

        listElement.appendChild(itemsList);
        listsContainer.appendChild(listElement);
    });
}

// Function to create a new list
function createList() {
    const listName = document.getElementById('list-name').value.trim();
    if (listName !== "") {
        const newList = {
            idx: lists.length + 1,
            name: listName,
            created: new Date(),
            items: []
        };
        lists.push(newList);
        localStorage.setItem('lists', JSON.stringify(lists));
        renderLists();
        document.getElementById('list-name').value = '';
    } else {
        alert("Please enter a list name.");
    }
}

// Function to toggle item status (checked/unchecked)
function toggleItem(listIdx, itemIdx) {
    const list = lists.find(list => list.idx === listIdx);
    const item = list.items.find(item => item.idx === itemIdx);
    item.checked = !item.checked;
    localStorage.setItem('lists', JSON.stringify(lists));
    renderLists();
}

// Function to show delete confirmation modal
function showModal(list) {
    const modal = document.getElementById('modal-container');
    modal.style.display = 'block';
    modal.dataset.listIdx = list.idx;
}

// Function to cancel delete operation
function cancelDelete() {
    const modal = document.getElementById('modal-container');
    modal.style.display = 'none';
}

// Function to confirm and delete list
function confirmDelete() {
    const modal = document.getElementById('modal-container');
    const listIdx = parseInt(modal.dataset.listIdx);
    lists = lists.filter(list => list.idx !== listIdx);
    localStorage.setItem('lists', JSON.stringify(lists));
    renderLists();
    modal.style.display = 'none';
}

// Render lists on page load
renderLists();

