document.querySelectorAll('.fa-eye-slash').forEach(function(element) {
  element.addEventListener('click', function() {
    const passwordInput = element.previousElementSibling;
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      element.classList.remove('fa-eye-slash');
      element.classList.add('fa-eye');
    } else {
      passwordInput.type = 'password';
      element.classList.remove('fa-eye');
      element.classList.add('fa-eye-slash');
    }
  });
});

document.querySelectorAll('.plus-icon').forEach(function(element) {
  element.addEventListener('click', function() {
    const popup = document.querySelector('.popup-container');
    if (popup.style.display === 'none' || popup.style.display === '') {
      popup.style.display = 'flex';
    } else {
      popup.style.display = 'none';
    }
  });
});

document.querySelectorAll('.close-icon').forEach(function(element) {
  element.addEventListener('click', function() {
    const popup = document.querySelector('.popup-container');
    popup.style.display = 'none';
  });
});

document.querySelectorAll('textarea').forEach(function(textarea) {
  textarea.addEventListener('input', function() {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px'; // Set height to scroll height
  });
});

document.querySelectorAll('.comment-opener').forEach(function(element) {
  element.addEventListener('click', function() {
    const commentSection = element.nextElementSibling;
    if (commentSection.style.display === 'none' || commentSection.style.display === '') {
      commentSection.style.display = 'flex';
    } else {
      commentSection.style.display = 'none';
    }
  });
});