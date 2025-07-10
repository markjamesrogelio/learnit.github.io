<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .main-content {
            padding: 30px;
        }

        .controls {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .category-filter {
            min-width: 200px;
        }

        .category-filter select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 16px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-filter select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .add-video-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .add-video-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .upload-form-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
            display: none;
        }

        .upload-form {
            display: grid;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .upload-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            justify-self: start;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        .upload-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
            display: none;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            width: 0%;
            transition: width 0.3s ease;
        }

        .file-info {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 14px;
            color: #666;
            display: none;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .video-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #f0f0f0;
        }

        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .video-thumbnail {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            position: relative;
            overflow: hidden;
        }

        .video-thumbnail::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
        }

        .video-thumbnail video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .play-icon {
            z-index: 1;
            position: relative;
        }

        .video-info {
            padding: 20px;
        }

        .video-title {
            font-size: 1.3em;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.4;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #666;
        }

        .video-category {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 500;
        }

        .video-likes {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .video-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .video-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 15px 15px 0 0;
        }

        .modal-body {
            padding: 30px;
        }

        .close {
            color: white;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            opacity: 0.8;
        }

        .video-player {
            width: 100%;
            max-height: 400px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 2em;
            font-weight: 600;
            color: #667eea;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box,
            .category-filter {
                min-width: 100%;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .video-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Video Management System</h1>
            <p>Upload, organize, and manage your educational videos</p>
        </div>

        <div class="main-content">
            <!-- Stats Section -->
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value" id="totalVideos">0</div>
                    <div class="stat-label">Total Videos</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="totalLikes">0</div>
                    <div class="stat-label">Total Likes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="totalCategories">0</div>
                    <div class="stat-label">Categories</div>
                </div>
            </div>

            <!-- Controls -->
            <div class="controls">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search videos by title or description...">
                    <span class="search-icon">🔍</span>
                </div>
                <div class="category-filter">
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <button class="add-video-btn" onclick="toggleUploadForm()">
                    ➕ Add New Video
                </button>
            </div>

            <!-- Messages -->
            <div id="messageContainer"></div>

            <!-- Upload Form -->
            <div id="uploadFormContainer" class="upload-form-container">
                <h2 style="margin-bottom: 20px; color: #333;">Upload New Video</h2>
                <form id="uploadForm" class="upload-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Video Title</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="course">Course</label>
                            <select id="course" name="course" required>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="videoFile">Video File</label>
                            <input type="file" id="videoFile" name="video_file" accept="video/*" required>
                            <div id="fileInfo" class="file-info"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="progress-bar" id="progressBar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <button type="submit" class="upload-btn" id="uploadBtn">Upload Video</button>
                </form>
            </div>

            <!-- Video Grid -->
            <div id="videoGrid" class="video-grid"></div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <h3>No videos found</h3>
                <p>Start by uploading your first video or try adjusting your search filters.</p>
            </div>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modalTitle">Video Details</h2>
            </div>
            <div class="modal-body">
                <video id="modalVideo" class="video-player" controls>
                    <source id="modalVideoSource" src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="video-details">
                    <h3 id="modalVideoTitle"></h3>
                    <div class="video-meta">
                        <span id="modalVideoCategory" class="video-category"></span>
                        <div class="video-likes">
                            <span>❤️</span>
                            <span id="modalVideoLikes">0</span>
                        </div>
                    </div>
                    <p id="modalVideoDescription"></p>
                    <p><strong>Course:</strong> <span id="modalVideoCourse"></span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data - replace with your actual data
        const categories = [
            'Science & Technology', 'Educational', 'Musical', 'Achievements', 
            'Culture, Arts, & Sports', 'Mathematics', 'Information Communication Technology',
            'Character Values Formation', 'Graduation', 'Academic Resources', 
            'Social Sciences', 'Events', 'The New Normal', 'Anniversary', 
            'Robotics', 'History', 'Business and Management', 'Trivia', 'Entertainment'
        ];

        const courses = [
            { id: 1, name: 'BS in Information Technology' },
            { id: 2, name: 'BS in Computer Science' },
            { id: 3, name: 'BS in Mathematics' },
            { id: 4, name: 'BS in Engineering' }
        ];

        const videos = [
            {
                id: 12,
                title: 'Introduction To Java',
                description: 'Brief 14 minutes introduction to learn Java',
                category: 'Information Communication Technology',
                course: 'BS in Information Technology',
                video_path: '74_1749105834.mp4',
                video_url: null,
                likes: 0,
                created_at: '2025-06-05 14:43:54'
            },
            {
                id: 13,
                title: 'Introduction to SQL',
                description: 'Discussion about SQL',
                category: 'Mathematics',
                course: 'BS in Information Technology',
                video_path: '74_1749106271.mp4',
                video_url: null,
                likes: 0,
                created_at: '2025-06-05 14:51:11'
            },
            {
                id: 14,
                title: 'Introduction to Python',
                description: 'Brief discussion for python',
                category: 'Robotics',
                course: 'BS in Information Technology',
                video_path: '74_1749106974.mp4',
                video_url: null,
                likes: 0,
                created_at: '2025-06-05 15:02:54'
            }
        ];

        let filteredVideos = [...videos];

        // Initialize the application
        function init() {
            populateCategories();
            populateCourses();
            renderVideos();
            updateStats();
            setupEventListeners();
        }

        // Populate category dropdowns
        function populateCategories() {
            const categoryFilter = document.getElementById('categoryFilter');
            const categorySelect = document.getElementById('category');
            
            categories.forEach(category => {
                const option1 = document.createElement('option');
                option1.value = category;
                option1.textContent = category;
                categoryFilter.appendChild(option1);
                
                const option2 = document.createElement('option');
                option2.value = category;
                option2.textContent = category;
                categorySelect.appendChild(option2);
            });
        }

        // Populate course dropdown
        function populateCourses() {
            const courseSelect = document.getElementById('course');
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.id;
                option.textContent = course.name;
                courseSelect.appendChild(option);
            });
        }

        // Render videos in grid
        function renderVideos() {
            const videoGrid = document.getElementById('videoGrid');
            const emptyState = document.getElementById('emptyState');
            
            if (filteredVideos.length === 0) {
                videoGrid.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }
            
            videoGrid.style.display = 'grid';
            emptyState.style.display = 'none';
            
            videoGrid.innerHTML = filteredVideos.map(video => `
                <div class="video-card" onclick="openVideoModal(${video.id})">
                    <div class="video-thumbnail">
                        ${video.video_url ? 
                            `<video muted><source src="${video.video_url}" type="video/mp4"></video>` : 
                            `<div class="play-icon">▶️</div>`
                        }
                    </div>
                    <div class="video-info">
                        <h3 class="video-title">${video.title}</h3>
                        <div class="video-meta">
                            <span class="video-category">${video.category}</span>
                            <div class="video-likes">
                                <span>❤️</span>
                                <span>${video.likes}</span>
                            </div>
                        </div>
                        <p class="video-description">${video.description}</p>
                        <p style="font-size: 0.9em; color: #666; margin-bottom: 15px;">
                            <strong>Course:</strong> ${video.course}
                        </p>
                        <div class="video-actions">
                            <button class="btn btn-danger" onclick="deleteVideo(event, ${video.id})">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Update statistics
        function updateStats() {
            const totalVideos = videos.length;
            const totalLikes = videos.reduce((sum, video) => sum + video.likes, 0);
            const uniqueCategories = [...new Set(videos.map(video => video.category))].length;
            
            document.getElementById('totalVideos').textContent = totalVideos;
            document.getElementById('totalLikes').textContent = totalLikes;
            document.getElementById('totalCategories').textContent = uniqueCategories;
        }

        // Setup event listeners
        function setupEventListeners() {
            document.getElementById('searchInput').addEventListener('input', handleSearch);
            document.getElementById('categoryFilter').addEventListener('change', handleCategoryFilter);
            document.getElementById('uploadForm').addEventListener('submit', handleUpload);
            document.getElementById('videoFile').addEventListener('change', handleFileSelect);
        }

        // Handle file selection
        function handleFileSelect(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById('fileInfo');
            
            if (file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileType = file.type;
                
                fileInfo.innerHTML = `
                    <strong>Selected:</strong> ${file.name}<br>
                    <strong>Size:</strong> ${fileSize} MB<br>
                    <strong>Type:</strong> ${fileType}
                `;
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        }

        // Handle search
        function handleSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            
            filteredVideos = videos.filter(video => {
                const matchesSearch = video.title.toLowerCase().includes(searchTerm) || 
                                    video.description.toLowerCase().includes(searchTerm);
                const matchesCategory = !categoryFilter || video.category === categoryFilter;
                return matchesSearch && matchesCategory;
            });
            
            renderVideos();
        }

        // Handle category filter
        function handleCategoryFilter() {
            const categoryFilter = document.getElementById('categoryFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            
            filteredVideos = videos.filter(video => {
                const matchesSearch = video.title.toLowerCase().includes(searchTerm) || 
                                    video.description.toLowerCase().includes(searchTerm);
                const matchesCategory = !categoryFilter || video.category === categoryFilter;
                return matchesSearch && matchesCategory;
            });
            
            renderVideos();
        }

        // Toggle upload form
        function toggleUploadForm() {
            const form = document.getElementById('uploadFormContainer');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        // Handle upload
        async function handleUpload(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const title = formData.get('title');
            const description = formData.get('description');
            const category = formData.get('category');
            const courseId = formData.get('course');
            const videoFile = formData.get('video_file');
            
            if (!title || !description || !category || !courseId || !videoFile) {
                showMessage('Please fill in all fields', 'error');
                return;
            }

            // Validate file type
            if (!videoFile.type.startsWith('video/')) {
                showMessage('Please select a valid video file', 'error');
                return;
            }

            // Validate file size (100MB limit)
            const maxSize = 100 * 1024 * 1024; // 100MB
            if (videoFile.size > maxSize) {
                showMessage('File size must be less than 100MB', 'error');
                return;
            }

            const uploadBtn = document.getElementById('uploadBtn');
            const progressBar = document.getElementById('progressBar');
            const progressFill = document.getElementById('progressFill');
            
            try {
                // Disable upload button and show progress
                uploadBtn.disabled = true;
                uploadBtn.textContent = 'Uploading...';
                progressBar.style.display = 'block';
                
                // Create video URL for preview
                const videoUrl = URL.createObjectURL(videoFile);
                
                // Simulate upload progress
                for (let i = 0; i <= 100; i += 10) {
                    progressFill.style.width = i + '%';
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
                
                // Find course name
                const courseName = courses.find(c => c.id == courseId)?.name || 'Unknown Course';
                
                // Create new video object
                const newVideo = {
                    id: Date.now(), // Use timestamp as ID
                    title: title,
                    description: description,
                    category: category,
                    course: courseName,
                    video_path: videoFile.name,
                    video_url: videoUrl,
                    likes: 0,
                    created_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
                };
                
                // Add to videos array
                videos.push(newVideo);
                filteredVideos = [...videos];
                
                // Update UI
                renderVideos();
                updateStats();
                
                // Success message
                showMessage('Video uploaded successfully!', 'success');
                
                // Reset form
                form.reset();
                document.getElementById('fileInfo').style.display = 'none';
                toggleUploadForm();
                
            } catch (error) {
                console.error('Upload error:', error);
                showMessage('Upload failed. Please try again.', 'error');
            } finally {
                // Reset upload button and hide progress
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload Video';
                progressBar.style.display = 'none';
                progressFill.style.width = '0%';
            }
        }

        // Show message
        function showMessage(message, type) {
            const messageContainer = document.getElementById('messageContainer');
            messageContainer.innerHTML = `
                <div class="message ${type}">
                    ${message}
                </div>
            `;
            
            setTimeout(() => {
                messageContainer.innerHTML = '';
            }, 5000);
        }

        // Open video modal
        function openVideoModal(videoId) {
            const video = videos.find(v => v.id === videoId);
            if (!video) return;
            
            document.getElementById('modalTitle').textContent = 'Video Details';
            document.getElementById('modalVideoTitle').textContent = video.title;
            document.getElementById('modalVideoCategory').textContent = video.category;
            document.getElementById('modalVideoLikes').textContent = video.likes;
            document.getElementById('modalVideoDescription').textContent = video.description;
            document.getElementById('modalVideoCourse').textContent = video.course;
            
            // Set video source
            const videoSrc = video.video_url || `../uploads/videos/${video.video_path}`;
            document.getElementById('modalVideoSource').src = videoSrc;
            document.getElementById('modalVideo').load();
            
            document.getElementById('videoModal').style.display = 'block';
        }

        // Close modal
        function closeModal() {
            document.getElementById('videoModal').style.display = 'none';
            document.getElementById('modalVideo').pause();
        }

        // Delete video
        function deleteVideo(event, videoId) {
            event.stopPropagation();
            
            if (confirm('Are you sure you want to delete this video?')) {
                // Find and remove video
                const index = videos.findIndex(v => v.id === videoId);
                if (index > -1) {
                    const video = videos[index];
                    
                    // Revoke object URL to free memory
                    if (video.video_url) {
                        URL.revokeObjectURL(video.video_url);
                    }
                    
                    // Remove from arrays
                    videos.splice(index, 1);
                    filteredVideos = filteredVideos.filter(v => v.id !== videoId);
                    
                    // Update UI
                    renderVideos();
                    updateStats();
                    
                    showMessage('Video deleted successfully!', 'success');
                }
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('videoModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>