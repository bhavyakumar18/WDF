<?php $pageTitle='Classes'; require 'inc/header.php'; ?>
<div class="page-heading"><div><div class="eyebrow">YOUR LEARNING</div><h1>Classes</h1><p>Search your subjects and explore class details.</p></div></div>
<div class="toolbar"><label class="search-box">⌕ <input id="classSearch" type="search" placeholder="Search classes..." aria-label="Search classes"></label><select id="classFilter" aria-label="Filter by category"><option value="all">All subjects</option><option value="technology">Technology</option><option value="foundation">Foundation</option></select><select id="classSort" aria-label="Sort classes"><option value="name">Name A–Z</option><option value="teacher">Teacher A–Z</option></select></div>
<div id="classStatus" class="muted small" aria-live="polite">Loading classes…</div><div id="classGrid" class="class-grid"></div><div class="pagination"><button id="prevPage" class="btn secondary" type="button">← Previous</button><span id="pageInfo">Page 1</span><button id="nextPage" class="btn secondary" type="button">Next →</button></div>
<script src="assets/classes.js" defer></script>
<?php require 'inc/footer.php'; ?>
