document.addEventListener('DOMContentLoaded', function() {
    const articles = document.querySelectorAll('.articles article');
    
    articles.forEach((article, index) => {
        article.style.opacity = '0';
        article.style.transform = 'translateY(20px)';
        article.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            article.style.opacity = '1';
            article.style.transform = 'translateY(0)';
        }, 100 * index);
    });
    
    articles.forEach(article => {
        article.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.2)';
        });
        
        article.addEventListener('mouseleave', function() {
            this.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.1)';
        });
    });
});
