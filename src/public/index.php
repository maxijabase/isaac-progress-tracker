<?php
	require_once(__DIR__ ."/../config.php");
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Binding of Isaac Unlock Progress Tracker</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	
	<link rel="apple-touch-icon" sizes="180x180" href="<?=htmlentities(DOCKER_PUBLIC_URI ."apple-touch-icon.png");?>">
	<link rel="icon" type="image/png" sizes="32x32" href="<?=htmlentities(DOCKER_PUBLIC_URI ."favicon-32x32.png");?>">
	<link rel="icon" type="image/png" sizes="16x16" href="<?=htmlentities(DOCKER_PUBLIC_URI ."favicon-16x16.png");?>">
	<link rel="manifest" href="<?=htmlentities(DOCKER_PUBLIC_URI ."site.webmanifest");?>">
</head>
<body data-bs-theme="dark">
	<nav class="navbar navbar-expand-lg bg-black">
		<div class="container-fluid">
			<div class="d-flex flex-row align-items-center">
				<a href="<?=htmlentities(DOCKER_PUBLIC_URI);?>" class="navbar-brand fw-bold">Isaac Tracker</a>
				
				<div class="nav-item ms-2">
					<a href="#" class="nav-link d-md-none" data-bs-toggle="modal" data-bs-target="#info-modal">
						<span class="badge rounded-pill text-bg-dark">?</span>
						<span class="visually-hidden">Info</span>
					</a>
					
					<a href="#" class="nav-link d-none d-md-inline-block" data-bs-toggle="modal" data-bs-target="#info-modal">
						<span class="badge rounded-pill text-bg-dark">?</span>
						<span class="visually-hidden">Info</span>
					</a>
				</div>
			</div>
			
			<span class="navbar-text ms-auto me-3 small d-none d-md-inline-block unlock_progress_text">Loading...</span>
			
			<ul class="navbar-nav d-flex flex-row align-items-center">
				<li class="nav-item me-3 d-none" id="view-toggle-container">
					<div class="form-check form-switch mb-0">
						<input class="form-check-input" type="checkbox" role="switch" id="view-toggle" checked>
						<label class="form-check-label small text-nowrap" for="view-toggle" id="view-toggle-label">My Progress</label>
					</div>
				</li>
				
				<li class="nav-item d-md-none">
					<a class="btn btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#steam-id-modal">Sync</a>
				</li>
				
				<li class="nav-item">
					<a class="btn btn-primary d-none d-md-inline-block" href="#" data-bs-toggle="modal" data-bs-target="#steam-id-modal">
						Sync Progress
					</a>
				</li>
			</ul>
		</div>
	</nav>
	
	<div class="d-md-none text-center py-2 text-muted unlock_progress_text"></div>
	
	<div class="container-md px-1 px-md-3 my-1 my-md-3">
		<div id="unlocks_table_progress" class="py-4">
			<div class="text-center my-3">
				<div class="h2">Loading...</div>
			</div>
			
			<div class="progress">
				<div class="progress-bar progress-bar-striped progress-bar-animated" id="unlocks_table_progress_value" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
			</div>
		</div>
		
		<div class="d-none" id="unlocks_table_container">
			<div class="d-flex flex-row flex-wrap flex-md-nowrap justify-content-between align-items-center px-1 px-sm-2 py-2 bg-body-tertiary mb-3" id="table-manager">
				<div class="flex-fill me-md-2 mb-2 mb-md-0">
					<input type="search" class="form-control" id="table-search" placeholder="Search...">
				</div>
				
				<div class="flex-fill me-md-2 mb-2 mb-md-0">
					<select class="form-select" id="table-filter">
						<option value="">All Unlocks</option>
						
						<optgroup label="Characters" id="character-filter"></optgroup>
						
						<optgroup label="Bosses" id="boss-filter"></optgroup>
						
						<optgroup label="Categories" id="category-filter"></optgroup>
					</select>
				</div>
				
				<div class="flex-fill me-2">
					<select class="form-select" id="table-sort">
						<option value="percentage">Sort by Percentage</option>
						<option value="name">Sort by Title</option>
						<option value="character">Sort by Character</option>
						<option value="boss">Sort by Boss</option>
						<option value="category">Sort by Category</option>
					</select>
				</div>
				
				<div>
					<button type="button" class="btn btn-dark" id="filters-reset">Reset</button>
				</div>
			</div>
			
			<div class="table-responsive">
				<table class="table table-hover position-relative" id="unlocks_table">
					<thead>
						<tr>
							<th width="64"></th>
							<th>Unlock</th>
							<th class="text-center d-none d-md-table-cell" width="100">Character</th>
							<th class="text-center d-none d-md-table-cell" width="100">Boss</th>
							<th class="d-none d-md-table-cell" width="100">Category</th>
							<th class="text-center d-none d-md-table-cell" title="Percentage of players that have this achievement">%</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		
		<div class="d-flex flex-column flex-md-row justify-content-between my-3 mx-2 mx-md-0">
			<div class="mb-2 mb-md-0">
				<small>Data from: <a href="https://theriebel.de/tboirah/" rel="noopener noreferrer nofollow" class="text-muted" target="_blank">Rebirth Achievement Helper</a>, <a href="https://bindingofisaacrebirth.fandom.com/" rel="noopener noreferrer nofollow" class="text-muted" target="_blank">Rebirth Wiki</a></small>
			</div>
			
			<div>
				<small><a href="#" class="text-muted" data-bs-toggle="modal" data-bs-target="#privacy-modal">Privacy Policy</a></small>
			</div>
		</div>
	</div>
	
	<!-- bootstrap modal: info -->
	<div class="modal fade" id="info-modal" tabindex="-1" aria-labelledby="info-modal-label" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="info-modal-label">Info</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>This tool helps you track progress in The Binding of Isaac: Rebirth by showing you which achievements you haven't unlocked yet.</p>
					<p>The <strong>Percentage (%)</strong> column shows the percentage of players that have unlocked that achievement.</p>
					<p class="d-none d-md-block">The <strong>Character</strong> and <strong>Boss</strong> columns show the character or boss required to unlock that achievement.</p>
					<p>This app is inspired by <a href="https://theriebel.de/tboirah/" class="text-muted" rel="noopener noreferrer nofollow" target="_blank">Rebirth Achievement Helper</a> and uses data from the <a href="https://bindingofisaacrebirth.fandom.com/" class="text-muted" rel="noopener noreferrer nofollow" target="_blank">Rebirth Wiki</a>.</p>
					<p>Source code is publicly available on <a href="https://github.com/donwilson/isaac-progress-tracker" target="_blank" rel="noopener noreferrer nofollow" class="text-muted fw-bold">GitHub</a>.</p>
				</div>
			</div>
		</div>
	</div>
	
	<!-- bootstrap modal form: set up a modal form for the user to input their Steam ID -->
	<div class="modal fade" id="steam-id-modal" tabindex="-1" aria-labelledby="steam-id-modal-label" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<form action="#" method="get" class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="steam-id-modal-label">Sync Progress</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label for="steam-api-key" class="form-label">Steam API Key</label>
						<input type="password" class="form-control" id="steam-api-key" name="steam-api-key" required placeholder="Your 32-character API key">
						<small class="text-muted">Get yours at <a href="https://steamcommunity.com/dev/apikey" rel="noopener noreferrer nofollow" target="_blank">steamcommunity.com/dev/apikey</a> &mdash; stored locally in your browser, never sent to our server.</small>
					</div>
					
					<div class="mb-3">
						<label for="steam-id" class="form-label">Steam ID</label>
						<input type="text" class="form-control" id="steam-id" name="steam-id" required placeholder="Example: 12345678901234567">
					</div>
					
					<div class="mt-3">
						<small class="text-muted">This automated progress tracker only works with the Steam version of <a href="https://store.steampowered.com/app/250900/" rel="noopener noreferrer nofollow" target="_blank">The Binding of Isaac: Rebirth</a>.</small>
					</div>
					
					<div class="mt-3">
						<small class="text-muted">You can find your Steam ID by using <a href="https://www.steamidfinder.com/" rel="noopener noreferrer nofollow" target="_blank">SteamID Finder</a>. You're looking for the <strong>"steamID64 (Dec)"</strong> number, it should be 17 characters long.</small>
					</div>
					
					<div class="mt-3">
						<small class="text-muted">If you're having issues syncing your unlocks, make sure your profile is <a href="https://help.steampowered.com/en/faqs/view/588C-C67D-0251-C276" rel="noopener noreferrer nofollow" target="_blank">set to public</a>.</small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</form>
		</div>
	</div>
	
	<!-- bootstrap modal: privacy info -->
	<div class="modal fade" id="privacy-modal" tabindex="-1" aria-labelledby="privacy-modal-label" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="privacy-modal-label">Privacy Policy</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>This website uses your browser's local storage functionality to keep track of your Steam API Key, Steam ID, and Binding of Isaac Steam achievement data. This is automatically stored on your device and is not transmitted to any server.</p>
					
					<p>Your Steam API Key is sent directly to Steam's servers to fetch your achievement data &mdash; it never passes through our servers in a way that we can store or read it.</p>
					
					<p>To remove your credentials or achievement data from your browser, you can clear your browser's local storage to remove all data stored by this website.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- toast container for notifications -->
	<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>
	
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/floatthead@2.2.5/dist/jquery.floatThead.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
	<script type="text/javascript">
		const observer = lozad();
		observer.observe();
	</script>
	<script>
		// function: show toast notification
		function showNotification(message, type, options) {
			type = type || 'error';
			options = options || {};
			
			var toastContainer = document.getElementById('toast-container');
			var toastId = 'toast-' + Date.now();
			
			// determine colors based on type
			var bgClass = 'bg-danger text-white';
			var closeClass = 'btn-close-white';
			var linkClass = 'text-white';
			var icon = '⚠️';
			var title = 'Error';
			
			if(type === 'success') {
				bgClass = 'bg-success text-white';
				icon = '✓';
				title = 'Success';
			} else if(type === 'warning') {
				bgClass = 'bg-warning text-dark';
				closeClass = '';
				linkClass = 'text-dark';
				icon = '⚠';
				title = 'Warning';
			} else if(type === 'info') {
				bgClass = 'bg-info text-dark';
				closeClass = '';
				linkClass = 'text-dark';
				icon = 'ℹ';
				title = 'Info';
			}
			
			// build extra content (like links)
			var extraContent = '';
			if(options.link) {
				extraContent = '<div class="mt-2"><a href="' + options.link.url + '" target="_blank" rel="noopener noreferrer" class="' + linkClass + '">' + options.link.text + '</a></div>';
			}
			
			var toastHtml = '<div id="' + toastId + '" class="toast ' + bgClass + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
				'<div class="d-flex">' +
					'<div class="toast-body">' +
						'<strong>' + icon + ' ' + title + '</strong><br>' +
						message +
						extraContent +
					'</div>' +
					'<button type="button" class="btn-close ' + closeClass + ' me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
				'</div>' +
			'</div>';
			
			toastContainer.insertAdjacentHTML('beforeend', toastHtml);
			
			var toastElement = document.getElementById(toastId);
			var toast = new bootstrap.Toast(toastElement, {
				autohide: type !== 'error',
				delay: 5000
			});
			toast.show();
			
			// remove from DOM after hidden
			toastElement.addEventListener('hidden.bs.toast', function() {
				toastElement.remove();
			});
		}
	</script>
	<script>
		$(() => $('table').floatThead());
		
		// store the progress data in a global variable
		window.my_progress = {};
		
		// application constants
		const STORAGE_KEY_PROGRESS = "my-progress";
		const STORAGE_KEY_STEAM_ID = "steam-id";
		const STORAGE_KEY_API_KEY = "steam-api-key";
		const STORAGE_KEY_UNLOCKS = "isaac-unlocks";
		
		// function: set loading state for Steam API sync
		function setLoadingState(isLoading) {
			const submitBtn = document.querySelector('#steam-id-modal button[type="submit"]');
			const progressTexts = document.querySelectorAll('.unlock_progress_text');
			const apiKeyInput = document.getElementById('steam-api-key');
			const steamIdInput = document.getElementById('steam-id');
			
			if(isLoading) {
				// disable inputs and button
				if(submitBtn) {
					submitBtn.disabled = true;
					submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Syncing...';
				}
				if(apiKeyInput) apiKeyInput.disabled = true;
				if(steamIdInput) steamIdInput.disabled = true;
				
				// update progress text
				progressTexts.forEach(function(el) {
					el.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Syncing with Steam...';
				});
			} else {
				// re-enable inputs and button
				if(submitBtn) {
					submitBtn.disabled = false;
					submitBtn.textContent = 'Update';
				}
				if(apiKeyInput) apiKeyInput.disabled = false;
				if(steamIdInput) steamIdInput.disabled = false;
			}
		}
		
		// function: update the progress bar
		function update_loading_bar_percent(value) {
			let progress_value_el = document.getElementById("unlocks_table_progress_value");
			
			value = Math.min(100, Math.max(0, value));
			
			progress_value_el.style.width = value +"%";
			progress_value_el.textContent = value +"%";
			progress_value_el.setAttribute("aria-valuenow", value);
		}
		
		// filter: characters
		function update_characters_filter() {
			// count the number of unlocks per character, update the character filter
			const characterCounts = {};
			const showMyProgressOnly = getViewMode();
			const selector = showMyProgressOnly ? "#unlocks_table tbody tr.unlock-incomplete" : "#unlocks_table tbody tr";
			
			document.querySelectorAll(selector).forEach(function(row) {
				const character = row.getAttribute("data-character");
				
				if(undefined === characterCounts[character]) {
					characterCounts[character] = 0;
				}
				
				characterCounts[character]++;
			});
			
			document.getElementById("character-filter").querySelectorAll("option").forEach(function(option) {
				const character = option.value;
				
				if('' === character) {
					return;
				}
				
				let text_content = option.textContent.replace(/\s*\([0-9]+\)$/, "");
				
				if(undefined !== characterCounts[character]) {
					text_content = text_content +" ("+ characterCounts[character] +")";
				}
				
				option.textContent = text_content;
			});
		}
		
		// filter: bosses
		function update_bosses_filter() {
			// count the number of unlocks per boss, update the boss filter
			const bossCounts = {};
			const showMyProgressOnly = getViewMode();
			const selector = showMyProgressOnly ? "#unlocks_table tbody tr.unlock-incomplete" : "#unlocks_table tbody tr";
			
			document.querySelectorAll(selector).forEach(function(row) {
				const boss = row.getAttribute("data-boss");
				
				if(undefined === bossCounts[boss]) {
					bossCounts[boss] = 0;
				}
				
				bossCounts[boss]++;
			});
			
			document.getElementById("boss-filter").querySelectorAll("option").forEach(function(option) {
				const boss = option.value;
				
				if('' === boss) {
					return;
				}
				
				let text_content = option.textContent.replace(/\s*\([0-9]+\)$/, "");
				
				if(undefined !== bossCounts[boss]) {
					text_content = text_content +" ("+ bossCounts[boss] +")";
				}
				
				option.textContent = text_content;
			});
		}
		
		// filter: categories
		function update_category_filter() {
			// count the number of unlocks per category, update the category filter
			const categoryCounts = {};
			const showMyProgressOnly = getViewMode();
			const selector = showMyProgressOnly ? "#unlocks_table tbody tr.unlock-incomplete" : "#unlocks_table tbody tr";
			
			document.querySelectorAll(selector).forEach(function(row) {
				const category = row.getAttribute("data-category-id");
				
				if(undefined === categoryCounts[category]) {
					categoryCounts[category] = 0;
				}
				
				categoryCounts[category]++;
			});
			
			document.getElementById("category-filter").querySelectorAll("option").forEach(function(option) {
				const category = option.value;
				
				if('' === category) {
					return;
				}
				
				let text_content = option.textContent.replace(/\s*\([0-9]+\)$/, "");
				
				if(undefined !== categoryCounts[category]) {
					text_content = text_content +" ("+ categoryCounts[category] +")";
				}
				
				option.textContent = text_content;
			});
		}
		
		// action: sanitize filters
		function sanitize_filter_options() {
			// remove filters with no count
			document.querySelectorAll("#table-manager select").forEach(function(select) {
				if(!select.getAttribute("id").match(/-filter$/)) {
					return;
				}
				
				select.querySelectorAll("option").forEach(function(option) {
					if(option.textContent.match(/\([0-9]+\)$/) || option.textContent.match(/^All /)) {
						return;
					}
					
					option.style.display = "none";
				});
			});
		}
		
		// action: update all filters
		function update_filters() {
			update_category_filter();
			update_characters_filter();
			update_bosses_filter();
			sanitize_filter_options();
		}
		
		// event: filter the table by search
		document.getElementById("table-search").addEventListener("input", function() {
			// reset the other filters
			document.getElementById("table-filter").value = "";
			
			// sanitize the search query
			const search = this.value.toLowerCase();
			const showMyProgressOnly = getViewMode();
			
			// filter the table rows
			document.querySelectorAll("#unlocks_table tbody tr").forEach(function(row) {
				// if in "My Progress" mode, keep completed achievements hidden
				if(showMyProgressOnly && row.classList.contains("unlock-complete")) {
					row.style.display = "none";
					return;
				}
				
				// search unlock name
				let search_text = row.getAttribute('data-search-data') || '';
				
				if(search_text.includes(search)) {
					row.style.display = "";
				} else {
					row.style.display = "none";
				}
			});
		});
		
		// event: filter the table by category
		document.getElementById("table-filter").addEventListener("change", function() {
			const filter = this.selectedOptions[0].getAttribute("data-filter") || '';
			const value = this.value;
			const showMyProgressOnly = getViewMode();
			
			document.querySelectorAll("#unlocks_table tbody tr").forEach(function(row) {
				// if in "My Progress" mode, keep completed achievements hidden
				if(showMyProgressOnly && row.classList.contains("unlock-complete")) {
					row.style.display = "none";
					return;
				}
				
				if(('' === value) || ('' === filter)) {
					row.style.display = "";
				} else {
					let rowValue = row.getAttribute("data-"+ filter) || '';
					
					if(row.getAttribute("data-"+ filter) == value) {
						row.style.display = "";
					} else {
						row.style.display = "none";
					}
				}
			});
			
			// reset the other filters
			document.getElementById("character-filter").value = "";
			document.getElementById("boss-filter").value = "";
		});
		
		// event: sort the table
		document.getElementById("table-sort").addEventListener("change", function() {
			const sort = this.value;
			
			const tbody = document.querySelector("#unlocks_table tbody");
			const rows = Array.from(tbody.querySelectorAll("tr"));
			
			rows.sort(function(a, b) {
				if("percentage" === sort) {
					const aSort = a.getAttribute("data-percentage");
					const bSort = b.getAttribute("data-percentage");
					
					return bSort - aSort;
				}
				
				let aContent = "";
				let bContent = "";
				
				if("name" === sort) {
					aContent = a.getAttribute("data-name");
					bContent = b.getAttribute("data-name");
				} else if("character" === sort) {
					aContent = a.getAttribute("data-character");
					bContent = b.getAttribute("data-character");
				} else if("boss" === sort) {
					aContent = a.getAttribute("data-boss");
					bContent = b.getAttribute("data-boss");
				} else if("category" === sort) {
					aContent = a.getAttribute("data-category");
					bContent = b.getAttribute("data-category");
				}
				
				if(aContent === bContent) {
					return 0;
				} else if(!aContent && bContent) {
					return 1;
				} else if(aContent && !bContent) {
					return -1;
				}
				
				return aContent.localeCompare(bContent);
			});
			
			rows.forEach(function(row) {
				tbody.appendChild(row);
			});
		});
		
		// event: reset the filters
		document.getElementById("filters-reset").addEventListener("click", function() {
			document.getElementById("table-search").value = "";
			document.getElementById("table-filter").value = "";
			document.getElementById("table-sort").value = "percentage";
			
			// reset visibility based on current view mode
			update_my_progress();
			
			document.getElementById("table-sort").dispatchEvent(new Event("change"));
		});
		
		// function: inject a row into the unlocks table
		function inject_unlock_table_row(unlock, categories) {
			let row = document.createElement("tr");
			let cell, div, span, link, img, strong, small, p = null;
			
			// search data
			let category_name = "";
			
			if(unlock.category !== false && unlock.category in categories) {
				category_name = categories[unlock.category];
			}
			
			let character_name = unlock.as === false ? "" : unlock.as;
			let boss_name = unlock.boss === false ? "" : unlock.boss;
			
			let search_datas = [
				unlock.name,
				unlock.displayName,
				unlock.description,
				unlock.unlockMethod,
				character_name,
				boss_name
			];
			
			let search_data = search_datas.join(" ").toLowerCase().trim();
			
			row.classList.add("unlock-incomplete");
			row.setAttribute("data-id", unlock.name);
			row.setAttribute("data-name", unlock.displayName);
			row.setAttribute("data-category", category_name);
			row.setAttribute("data-category-id", unlock.category);
			row.setAttribute("data-character", character_name);
			row.setAttribute("data-boss", boss_name);
			row.setAttribute("data-percentage", unlock.percentage);
			row.setAttribute("data-search-data", search_data);
			
			// image
			cell = document.createElement("td");
			
			link = document.createElement("a");
			link.href = "https://bindingofisaacrebirth.fandom.com/wiki/".concat(unlock.link);
			link.target = "_blank";
			link.rel = "noopener noreferrer nofollow";
			
			img = document.createElement("img");
			
			img.src = "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
			img.setAttribute("data-src", unlock.icon);
			img.alt = unlock.displayName;
			img.width = 64;
			img.height = 64;
			img.classList.add("lozad");
			
			link.appendChild(img);
			cell.appendChild(link);
			
			// mobile: percentage
			div = document.createElement("div");
			div.classList.add("small", "text-center", "text-muted", "d-block", "d-md-none", "pt-1");
			div.textContent = unlock.percentage.toFixed(0) +"%";
			
			cell.appendChild(div);
			
			row.appendChild(cell);
			
			// unlock
			cell = document.createElement("td");
			link = document.createElement("a");
			link.href = "https://bindingofisaacrebirth.fandom.com/wiki/".concat(unlock.link);
			link.target = "_blank";
			link.rel = "noopener noreferrer nofollow";
			
			strong = document.createElement("strong");
			strong.title = unlock.name;
			strong.textContent = unlock.displayName;
			
			link.appendChild(strong);
			cell.appendChild(link);
			
			if(unlock.description !== "") {
				small = document.createElement("em");
				small.classList.add("small", "text-muted", "d-block", "d-md-inline-block", "ps-md-2", "mb-2", "mb-md-0");
				small.textContent = unlock.description;
				
				cell.appendChild(small);
			}
			
			if(unlock.unlockMethod !== "") {
				p = document.createElement("p");
				p.classList.add("small", "py-0", "my-0");
				p.textContent = unlock.unlockMethod;
				
				cell.appendChild(p);
			}
			
			// mobile: character
			if(unlock.as !== false) {
				div = document.createElement("div");
				div.classList.add("small", "d-block", "d-md-none", "pt-2");
				
				strong = document.createElement("strong");
				strong.textContent = "Character:";
				
				div.appendChild(strong);
				div.appendChild(document.createTextNode(" "+ unlock.as));
				
				cell.appendChild(div);
			}
			
			// mobile: boss
			if(unlock.boss !== false) {
				div = document.createElement("div");
				div.classList.add("small", "d-block", "d-md-none", "pt-2");
				
				strong = document.createElement("strong");
				strong.textContent = "Boss:";
				
				div.appendChild(strong);
				div.appendChild(document.createTextNode(" "+ unlock.boss));
				
				cell.appendChild(div);
			}
			
			// mobile: category
			if(unlock.category !== false && unlock.category in categories) {
				div = document.createElement("div");
				div.classList.add("small", "d-block", "d-md-none", "pt-2");
				
				strong = document.createElement("strong");
				strong.textContent = "Category:";
				
				div.appendChild(strong);
				div.appendChild(document.createTextNode(" "+ categories[unlock.category]));
				
				cell.appendChild(div);
			}
			
			row.appendChild(cell);
			
			// character
			cell = document.createElement("td");
			cell.classList.add("text-center", "d-none", "d-md-table-cell");
			
			if(unlock.as !== false) {
				cell.textContent = unlock.as;
			} else {
				span = document.createElement("span");
				span.classList.add("text-muted", "small");
				span.innerHTML = "&mdash;";
				
				cell.appendChild(span);
			}
			
			row.appendChild(cell);
			
			// boss
			cell = document.createElement("td");
			cell.classList.add("text-center", "d-none", "d-md-table-cell");
			
			if(unlock.boss !== false) {
				cell.textContent = unlock.boss;
			} else {
				span = document.createElement("span");
				span.classList.add("text-muted", "small", "fst-italic");
				span.innerHTML = "&mdash;";
				
				cell.appendChild(span);
			}
			
			row.appendChild(cell);
			
			// category
			cell = document.createElement("td");
			cell.classList.add("d-none", "d-md-table-cell");
			
			if(unlock.category !== false && unlock.category in categories) {
				cell.textContent = categories[unlock.category];
			} else {
				span = document.createElement("span");
				span.classList.add("text-muted", "small", "fst-italic");
				span.innerHTML = "&mdash;";
				
				cell.appendChild(span);
			}
			
			row.appendChild(cell);
			
			// percentage
			cell = document.createElement("td");
			cell.classList.add("text-center", "d-none", "d-md-table-cell");
			cell.setAttribute("data-sort", unlock.percentage);
			cell.textContent = unlock.percentage.toFixed(0) + "%";
			
			row.appendChild(cell);
			
			document.querySelector("#unlocks_table tbody").appendChild(row);
		}
		
		// function: ingest the unlocks data
		function ingest_unlocks_data(data) {
			if(!data) {
				showNotification("Failed to load data, please try again later.", "error");
				
				return;
			}
			
			if(!data.unlocks || !data.categories) {
				showNotification("Failed to load data, please try again later.", "error");
				
				return;
			}
			
			// update the progress bar
			update_loading_bar_percent(25);
			
			// build list of category names and ids, sorted by name
			let sorted_category_rows = Object.keys(data.categories).map(function(key) {
				return {
					id: key,
					name: data.categories[key]
				};
			}).sort(function(a, b) {
				return a.name.localeCompare(b.name);
			});
			
			// add the categories to the category filter
			const categoryFilter = document.getElementById("category-filter");
			Object.keys(sorted_category_rows).forEach(function(key) {
				let category = sorted_category_rows[key];
				
				let option = document.createElement("option");
				option.value = category.id;
				option.textContent = category.name;
				option.setAttribute('data-filter', 'category-id');
				
				categoryFilter.appendChild(option);
			});
			
			update_loading_bar_percent(35);
			
			// add the characters to the character filter
			if(typeof data.character_names !== "undefined" && data.character_names.length > 0) {
				const characterFilter = document.getElementById("character-filter");
				Array.from(data.character_names).forEach(function(character) {
					let option = document.createElement("option");
					option.value = character;
					option.textContent = character;
					option.setAttribute('data-filter', 'character');
					
					characterFilter.appendChild(option);
				});
			}
			
			update_loading_bar_percent(45);
			
			// add the bosses to the boss filter
			if(typeof data.boss_names !== "undefined" && Object.keys(data.boss_names).length > 0) {
				const bossFilter = document.getElementById("boss-filter");
				Array.from(data.boss_names).forEach(function(boss) {
					let option = document.createElement("option");
					option.value = boss;
					option.textContent = boss;
					option.setAttribute('data-filter', 'boss');
					
					bossFilter.appendChild(option);
				});
			}
			
			update_loading_bar_percent(55);
			
			// update the table with the unlocks data
			Array.from(data.unlocks).forEach(function(unlock) {
				inject_unlock_table_row(unlock, data.categories);
			});
			
			update_loading_bar_percent(90);
			
			// update the progress data
			update_my_progress();
			
			// update the filters with the current state of the table
			update_filters();
			
			// update the progress bar
			update_loading_bar_percent(100);
			
			// sort the table by percentage
			document.getElementById("table-sort").dispatchEvent(new Event("change"));
			
			// lazy load images
			observer.observe();
			
			// show the table
			document.getElementById("unlocks_table_container").classList.remove("d-none");
			
			// hide the progress bar
			document.getElementById("unlocks_table_progress").classList.add("d-none");
		}
		
		function load_unlocks_data() {
			try {
				update_loading_bar_percent(0);
				
				if("undefined" === typeof localStorage) {
					throw new Error("localStorage is not available");
				}
				
				// first attempt to load the unlocks data from localStorage
				const unlocksData = localStorage.getItem(STORAGE_KEY_UNLOCKS);
				
				if(null !== unlocksData) {
					update_loading_bar_percent(10);
					
					ingest_unlocks_data(JSON.parse(unlocksData));
					
					return;
				}
				
				throw new Error("localStorage data is empty");
			} catch(error) {
				update_loading_bar_percent(10);
				
				// load in the unlocks data from the server
				fetch("<?=htmlentities(DOCKER_PUBLIC_URI);?>unlocks.json").then(function(response) {
					if(!response.ok) {
						throw new Error("Failed to fetch data from server");
					}
					
					update_loading_bar_percent(15);
					
					return response.json();
				}).then(function(json) {
					// check if localStorage is available in the browser
					if("undefined" !== typeof localStorage) {
						// save the data to localStorage
						localStorage.setItem(STORAGE_KEY_UNLOCKS, JSON.stringify(json));
					}
					
					// ingest the data
					ingest_unlocks_data(json);
				}).catch(function(error) {
					console.error(error);
					
					showNotification("Failed to load data: " + error.message, "error");
				});
			}
		}
		
		// function: format a number with commas
		function number_format(number, decimals) {
			decimals = decimals || 0;
			
			return number.toLocaleString(undefined, {
				minimumFractionDigits: decimals,
				maximumFractionDigits: decimals,
			});
		}
		
		// function: check if we have valid progress data
		function hasProgressData() {
			return ("undefined" !== typeof window.my_progress) && 
			       ("undefined" !== typeof window.my_progress.playerstats) && 
			       ("undefined" !== typeof window.my_progress.playerstats.achievements);
		}
		
		// function: get current view mode (true = my progress only, false = all achievements)
		function getViewMode() {
			return document.getElementById("view-toggle").checked;
		}
		
		// function: update the progress data based on the user's achievements
		function update_my_progress() {
			let num_unlocks = 0;
			let num_unlocked = 0;
			let num_remaining = 0;
			
			const showMyProgressOnly = getViewMode();
			const hasProgress = hasProgressData();
			
			// show/hide the view toggle based on whether we have progress data
			const toggleContainer = document.getElementById("view-toggle-container");
			if(hasProgress) {
				toggleContainer.classList.remove("d-none");
			} else {
				toggleContainer.classList.add("d-none");
			}
			
			document.querySelectorAll("#unlocks_table tbody tr").forEach(function(row) {
				const id = row.getAttribute("data-id");
				
				num_unlocks++;
				
				if(!hasProgress) {
					row.style.display = "";
					row.classList.remove("unlock-complete");
					row.classList.add("unlock-incomplete");
					
					return;
				}
				
				const achievement = window.my_progress.playerstats.achievements.find(function(achievement) {
					return achievement.name === id;
				});
				
				if(("undefined" !== typeof achievement) && (achievement.achieved === 1)) {
					// achievement is completed
					row.classList.remove("unlock-incomplete");
					row.classList.add("unlock-complete");
					
					// hide if showing "my progress" only, show if showing all
					row.style.display = showMyProgressOnly ? "none" : "";
					
					num_unlocked++;
				} else {
					row.style.display = "";
					row.classList.remove("unlock-complete");
					row.classList.add("unlock-incomplete");
					
					num_remaining++;
				}
			});
			
			let progress_text = "";
			
			if(num_unlocked > 0) {
				progress_text += number_format(num_unlocked) +"/"+ number_format(num_unlocks) +" - ";
			}
			
			progress_text += num_remaining +" remaining ("+ number_format((num_unlocks - num_unlocked) / num_unlocks * 100, 1) +"%)";
			
			// update all elements matching .unlock_progress_text with progress_text
			document.querySelectorAll(".unlock_progress_text").forEach(function(el) {
				el.textContent = progress_text;
			});
			
			// update the filters with the current state of the table
			update_filters();
		}
		
		// event: view toggle change
		document.getElementById("view-toggle").addEventListener("change", function() {
			const label = document.getElementById("view-toggle-label");
			label.textContent = this.checked ? "My Progress" : "All Achievements";
			update_my_progress();
		});
		
		// event: modal #steam-id-modal opens, attempt to fill the inputs from localStorage
		document.getElementById("steam-id-modal").addEventListener("show.bs.modal", function() {
			if("undefined" === typeof localStorage) {
				return;
			}
			
			const apiKey = localStorage.getItem(STORAGE_KEY_API_KEY);
			
			if(null !== apiKey) {
				document.getElementById("steam-api-key").value = apiKey;
			}
			
			const steamId = localStorage.getItem(STORAGE_KEY_STEAM_ID);
			
			if(null !== steamId) {
				document.getElementById("steam-id").value = steamId;
			}
		});
		
		// function: pull in a steam user's progress data
		function fetch_steam_user_progress(apiKey, steamId) {
			// show loading state
			setLoadingState(true);
			
			// fetch json data from our server-side proxy (which calls Steam API)
			fetch("<?=htmlentities(DOCKER_PUBLIC_URI);?>api/steam-progress.php", {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded",
				},
				body: "apikey=" + encodeURIComponent(apiKey) + "&steamid=" + encodeURIComponent(steamId)
			})
				.then(function(response) {
					if(!response.ok) {
						return response.json().then(function(errorData) {
							throw new Error(errorData.error || "Failed to fetch data from Steam API (HTTP " + response.status + ")");
						});
					}
					return response.json();
				})
				.then(function(json) {
					if(json.error) {
						throw new Error(json.error);
					}
					
					if("undefined" === typeof json.playerstats) {
						throw new Error("Invalid data from Steam API. Make sure your Steam profile is public.");
					}
					
					// save the data to window.my_progress
					window.my_progress = json;
					
					// save the data to localStorage (if available)
					if("undefined" !== typeof localStorage) {
						localStorage.setItem(STORAGE_KEY_PROGRESS, JSON.stringify(window.my_progress));
					}
					
					// update the progress data
					update_my_progress();
					
					// hide loading state
					setLoadingState(false);
				})
				.catch(function(error) {
					console.error(error);
					
					// hide loading state
					setLoadingState(false);
					
					// restore the progress text
					update_my_progress();
					
					// show an error message with Steam status link
					showNotification("Failed to fetch data from Steam API: " + error.message, "error", {
						link: {
							url: "https://steamstat.us/",
							text: "Check Steam status at steamstat.us →"
						}
					});
				});
		}
		
		// event: form submit, save the inputs to localStorage
		document.querySelector("form").addEventListener("submit", function(event) {
			// prevent the form from submitting
			event.preventDefault();
			
			// get the api key from the input
			let apiKey = document.getElementById("steam-api-key").value.trim();
			
			// validate the api key (should be 32 hex characters)
			if(!/^[A-Fa-f0-9]{32}$/.test(apiKey)) {
				showNotification("Invalid Steam API Key. It should be 32 characters (letters A-F and numbers).", "warning");
				
				return false;
			}
			
			// get the steam id from the input
			let steamId = document.getElementById("steam-id").value;
			
			// sanitize the steam id
			steamId = steamId.replace(/[^0-9]/g, "");
			
			if(17 !== steamId.length) {
				showNotification("Invalid Steam ID. It should be 17 digits.", "warning");
				
				return false;
			}
			
			// save the api key to localStorage
			if("undefined" !== typeof localStorage) {
				localStorage.setItem(STORAGE_KEY_API_KEY, apiKey);
			}
			
			// save the steam id to localStorage
			if("undefined" !== typeof localStorage) {
				localStorage.setItem(STORAGE_KEY_STEAM_ID, steamId);
			}
			
			// hide the modal
			const modal = bootstrap.Modal.getInstance(document.getElementById("steam-id-modal"));
			modal.hide();
			
			// add the steam id to the URL hash
			window.location.hash = steamId;
			
			// fetch the steam user's progress data
			fetch_steam_user_progress(apiKey, steamId);
		});
		
		
		// initialize the app
		try {
			// if the hash is the steam user ID and we have an API key, fetch the progress data
			if(window.location.hash.match(/^#[0-9]{17}$/)) {
				const steamId = window.location.hash.substr(1);
				const apiKey = localStorage.getItem(STORAGE_KEY_API_KEY);
				
				if("undefined" !== typeof localStorage) {
					localStorage.setItem(STORAGE_KEY_STEAM_ID, steamId);
				}
				
				// only fetch if we have both API key and Steam ID
				if(apiKey && /^[A-Fa-f0-9]{32}$/.test(apiKey)) {
					fetch_steam_user_progress(apiKey, steamId);
				}
			}
			
			load_unlocks_data();
			
			// load the progress data from localStorage (if available)
			if("undefined" !== typeof localStorage) {
				const myProgress = localStorage.getItem(STORAGE_KEY_PROGRESS) || null;
				
				if(null !== myProgress) {
					window.my_progress = JSON.parse(myProgress);
					
					update_my_progress();
				}
			}
		} catch(error) {
			console.error(error);
			
			showNotification("Failed to load data: " + error.message, "error");
		}
	</script>
</body>
</html>