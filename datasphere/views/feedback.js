// Create view and respond modals
document.addEventListener('DOMContentLoaded', function() {
    // Create view modal
    let viewModal = document.createElement('div');
    viewModal.id = 'viewFeedbackModal';
    viewModal.className = 'modal';
    viewModal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Feedback Details</h2>
                <span class="close" onclick="closeModal('viewFeedbackModal')">&times;</span>
            </div>
            <div class="feedback-detail">
                <p><span class="label">From:</span> <span id="feedback-user"></span></p>
                <p><span class="label">Date:</span> <span id="feedback-date"></span></p>
                <p><span class="label">Rating:</span> <span id="feedback-rating" class="stars"></span></p>
                <p><span class="label">Status:</span> <span id="feedback-status"></span></p>
                <p><span class="label">Content:</span></p>
                <div class="feedback-content" id="feedback-content"></div>
            </div>
            <div class="response-history" id="response-history">
                <h3>Response History</h3>
                <div id="responses-container"></div>
            </div>
        </div>
    `;

    // Create respond modal
    let respondModal = document.createElement('div');
    respondModal.id = 'respondFeedbackModal';
    respondModal.className = 'modal';
    respondModal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>Respond to Feedback</h2>
                <span class="close" onclick="closeModal('respondFeedbackModal')">&times;</span>
            </div>
            <div class="feedback-detail">
                <p><span class="label">From:</span> <span id="respond-feedback-user"></span></p>
                <p><span class="label">Date:</span> <span id="respond-feedback-date"></span></p>
                <p><span class="label">Rating:</span> <span id="respond-feedback-rating" class="stars"></span></p>
                <p><span class="label">Content:</span></p>
                <div class="feedback-content" id="respond-feedback-content"></div>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="feedback_id" id="respond-feedback-id">
                <div class="form-group">
                    <label for="response_text">Your Response:</label>
                    <textarea name="response_text" id="response_text" required></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="resolve" value="1"> Mark as resolved
                    </label>
                </div>
                <div class="form-actions">
                    <button type="button" onclick="closeModal('respondFeedbackModal')" class="btn-secondary">Cancel</button>
                    <button type="submit" name="send_response" class="btn-primary">Send Response</button>
                </div>
            </form>
        </div>
    `;

    // Add modals to the body
    document.body.appendChild(viewModal);
    document.body.appendChild(respondModal);
});

// Function to view feedback details
function viewFeedback(feedbackId) {
    // Fetch feedback details using AJAX
    fetch(`?get_feedback=true&id=${feedbackId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal with feedback data
            document.getElementById('feedback-user').textContent = data.user;
            document.getElementById('feedback-date').textContent = data.date;
            document.getElementById('feedback-rating').innerHTML = data.rating;
            
            // Set status with appropriate class
            let statusElem = document.getElementById('feedback-status');
            statusElem.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            statusElem.className = `status-badge status-${data.status}`;
            
            document.getElementById('feedback-content').textContent = data.content;
            
            // Clear and populate responses
            const responsesContainer = document.getElementById('responses-container');
            responsesContainer.innerHTML = '';
            
            if (data.responses && data.responses.length > 0) {
                data.responses.forEach(response => {
                    const responseItem = document.createElement('div');
                    responseItem.className = 'response-item';
                    responseItem.innerHTML = `
                        <div class="response-meta">
                            <span>By: ${response.admin}</span>
                            <span>${response.date}</span>
                        </div>
                        <div class="response-text">${response.text}</div>
                    `;
                    responsesContainer.appendChild(responseItem);
                });
            } else {
                responsesContainer.innerHTML = '<p>No responses yet.</p>';
            }
            
            // Show the modal
            document.getElementById('viewFeedbackModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching feedback:', error);
            alert('Failed to load feedback details.');
        });
}

// Function to open respond modal
function respondToFeedback(feedbackId) {
    // Fetch feedback details using AJAX
    fetch(`?get_feedback=true&id=${feedbackId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the modal with feedback data
            document.getElementById('respond-feedback-user').textContent = data.user;
            document.getElementById('respond-feedback-date').textContent = data.date;
            document.getElementById('respond-feedback-rating').innerHTML = data.rating;
            document.getElementById('respond-feedback-content').textContent = data.content;
            document.getElementById('respond-feedback-id').value = data.id;
            
            // Show the modal
            document.getElementById('respondFeedbackModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching feedback:', error);
            alert('Failed to load feedback details.');
        });
}

// Function to close modals
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close the modal when clicking outside of it
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
};