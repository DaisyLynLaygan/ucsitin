<?php
// resources.php
$pageTitle = "Resources";
include('header.php');
?>

<div class="resources-container">
    <div class="resources-header">
        <h1><i class="fas fa-book-open"></i> Google Drive Resources</h1>
        <p class="subtitle">All course materials including lecture slides, assignments are available in this shared Google Drive link</p>
    </div>
        <div class="resource-content">            
            <a href="https://drive.google.com/drive/folders/18bx8UxVLv301SdCZqZNNu-uYfRhRhWAv?usp=drive_link" 
               target="_blank" 
               class="resource-btn">
                <i class="fas fa-cloud-download-alt"></i> Access Resources
            </a>
        </div>
    </div>
    </div>
</div>

<style>
.resources-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 30px;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.resources-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e7ff;
}

.resources-header h1 {
    color: #3f51b5;
    font-size: 2.2rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.resources-header .subtitle {
    color: #6d6d6d;
    font-size: 1.1rem;
}

.resource-card {
    display: flex;
    background-color: var(--light-bg);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 30px;
    transition: transform 0.3s ease;
}

.resource-card:hover {
    transform: translateY(-5px);
}

.resource-icon {
    background-color: var(--primary-dark);
    color: white;
    width: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
}

.resource-content {
    padding: 30px 0 0 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.resource-content h2 {
    color: var(--primary-dark);
    margin-top: 0;
    margin-bottom: 15px;
}

.resource-content p {
    color: var(--secondary-dark);
    line-height: 1.6;
    margin-bottom: 25px;
}

.resource-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    background-color: #3f51b5;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(63,81,181,0.08);
}

.resource-btn i {
    color: white;
    font-size: 1.3em;
}

.resource-btn:hover {
    background-color: #303f9f;
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 4px 12px rgba(63,81,181,0.15);
}

.resource-info {
    background-color: var(--lighter-bg);
    border-radius: 8px;
    padding: 20px;
}

.info-box {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.info-box i {
    color: var(--primary-dark);
    font-size: 1.5rem;
    margin-top: 3px;
}

.info-box p {
    margin: 0;
    color: var(--secondary-dark);
    line-height: 1.6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .resources-container {
        padding: 16px;
    }
    .resources-header h1 {
        font-size: 1.5rem;
    }
    .resource-content {
        padding: 16px 0 0 0;
    }
}
</style>

