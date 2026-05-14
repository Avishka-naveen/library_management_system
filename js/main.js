  // Function to load HTML pages dynamically
    async function loadPage(pageName) {
        const mainContent = document.getElementById('mainContent');
        
         try {
            // Fetch the HTML file
            const response = await fetch(`${pageName}.php`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const html = await response.text();
            
            // Update main content with fetched HTML
            mainContent.innerHTML = html;
            
         
            
        } catch (error) {
            console.error('Error loading page:', error);
            mainContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Failed to load ${pageName}.html. Make sure the file exists!
                    <br><br>
                    <button class="btn btn-primary" onclick="loadPage('dashboard')">Go to Dashboard</button>
                </div>
            `;
        }
    }
    
    // Toggle sidebar collapse (desktop)
    function toggleCollapse() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
        
        
    }
    
    // Toggle mobile sidebar
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    
    // Handle navigation clicks
    document.querySelectorAll('.sidebar-nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get page name from data-page attribute
            const page = this.getAttribute('data-page');
            
            if (page && page !== 'logout') {
                // Remove active class from all items
                document.querySelectorAll('.sidebar-nav-item').forEach(nav => {
                    nav.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.classList.add('active');
                
                // Load the page
                loadPage(page);
                
                // Close mobile sidebar after navigation
                if (window.innerWidth <= 768) {
                    toggleMobileSidebar();
                }
            } 
        });
    });