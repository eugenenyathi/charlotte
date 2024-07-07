<template>
	<div class="search-results-container">
		<ul class="search-ul-wrapper">
			<li class="list-item">
				<div class="search-details">
					<span class="st-name">Dylan Baloyi</span>
					<span class="st-number">L0202783T</span>
				</div>
			</li>
			<li class="list-item">
				<div class="search-details">
					<span class="st-name">Thabelani Ncube</span>
					<span class="st-number">L0202783T</span>
				</div>
			</li>
			<li class="list-item">
				<div class="search-details">
					<span class="st-name">Munya Thomas</span>
					<span class="st-number">L0202783T</span>
				</div>
			</li>
		</ul>
	</div>

	<ul class="selected-ul-wrapper">
		<li class="list-item">
			Dylan Baloyi
			<ButtonIcon
				class="remove-roommate-btn"
				icon-name="fa-regular fa-trash-alt"
			/>
		</li>
		<li class="list-item">
			Thabelani Ncube
			<ButtonIcon
				class="remove-roommate-btn"
				icon-name="fa-regular fa-trash-alt"
			/>
		</li>
		<li class="list-item">
			Munyaradzi Mango
			<ButtonIcon
				class="remove-roommate-btn"
				icon-name="fa-regular fa-trash-alt"
			/>
		</li>
	</ul>

	<div class="residence-content">
		<h1 class="student-name">Living Space</h1>
		<div class="info-wrapper">
			<div class="info">
				<p>Hostel</p>
				<p>Suburb</p>
			</div>
			<div class="info">
				<p>Room</p>
				<p>215</p>
			</div>
			<div class="info">
				<p>Roommates</p>
				<p>3</p>
			</div>
			<div class="info">
				<p>Floor</p>
				<p>2</p>
			</div>
			<div class="info">
				<p>Side</p>
				<p>Left</p>
			</div>
			<div class="info">
				<p>Checked-in</p>
				<p>22 September 2022</p>
			</div>
		</div>
	</div>
</template>

<script>
collective.checkIn = dayjs(data.checkIn).format("DD-MM-YYYY");
collective.checkOut = dayjs(data.checkOut).format("DD-MM-YYYY");
collective.lastLogin = dayjs(data.lastLogin).format("dddd DD MMM");
</script>
