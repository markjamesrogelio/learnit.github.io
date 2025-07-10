document.addEventListener("DOMContentLoaded", function () {
    // Handle tab switching via AJAX
    document.querySelectorAll(".tab-btn").forEach(button => {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        const section = this.getAttribute("data-section");
  
        fetch(`load_section.php?section=${section}`)
          .then(res => res.text())
          .then(html => {
            document.querySelector(".admin-content").innerHTML = html;
  
            // Run showForm('student') again after content is replaced
            if (section === 'home') {
              showForm('student');
            }
          });
      });
    });
    
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("accept-btn")) {
    const id = e.target.getAttribute("data-id");
    updateStatus(id, 'approved');
  }
  if (e.target.classList.contains("reject-btn")) {
    const id = e.target.getAttribute("data-id");
    updateStatus(id, 'rejected');
  }
});

function updateStatus(userId, status) {
  fetch("update_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `id=${encodeURIComponent(userId)}&status=${encodeURIComponent(status)}`
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      location.reload(); // Refresh to see updated list
    })
    .catch(() => alert("Something went wrong."));
}
    // Handle AJAX form submissions
    document.addEventListener("submit", function (e) {
      if (e.target.classList.contains("ajax-form")) {
        e.preventDefault();
        const form = e.target;
        const type = form.getAttribute("data-type");
        const formData = new FormData(form);
  
        fetch(`save_${type}.php`, {
          method: "POST",
          body: formData
        })
          .then(res => res.text())
          .then(msg => {
            alert(msg);
            form.reset();
          })
          .catch(() => alert("An error occurred while submitting the form."));
      }
    });
  
    // Form toggling using .active class
    // Make showForm globally accessible
function showForm(type) {
    document.querySelectorAll('.form-content').forEach(f => f.classList.remove('active'));
    const target = document.getElementById('form-' + type);
    if (target) target.classList.add('active');
  }
  
  document.addEventListener("DOMContentLoaded", function () {
    // Handle tab switching via AJAX
    document.querySelectorAll(".tab-btn").forEach(button => {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        const section = this.getAttribute("data-section");
  
        fetch(`load_section.php?section=${section}`)
          .then(res => res.text())
          .then(html => {
            document.querySelector(".admin-content").innerHTML = html;
  
            // Run default form show again after reload
            if (section === 'home') {
              showForm('student');
            }
          });
      });
    });
  
    // Handle AJAX form submissions
    document.addEventListener("submit", function (e) {
      if (e.target.classList.contains("ajax-form")) {
        e.preventDefault();
        const form = e.target;
        const type = form.getAttribute("data-type");
        const formData = new FormData(form);
  
        fetch(`save_${type}.php`, {
          method: "POST",
          body: formData
        })
          .then(res => res.text())
          .then(msg => {
            alert(msg);
            form.reset();
          })
          .catch(() => alert("An error occurred while submitting the form."));
      }
    });

  });
  
  
  });
  