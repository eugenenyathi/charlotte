<template>
    <div class="collapse collapse-arrow join-item border border-base-300">
        <input v-if="index === 0" type="radio" name="my-accordion-4" checked="checked" />
        <input v-else type="radio" name="my-accordion-4" />
        <div class="collapse-title text-xl font-medium">
            {{ res.part }}
        </div>
        <div class="collapse-content">
            <div class="info-wrapper">
                <div class="info">
                    <p>Hostel</p>
                    <p>{{ res.hostel }}</p>
                </div>
                <div class="info">
                    <p>Wing</p>
                    <p>{{ res.wing }}</p>
                </div>
                <div class="info">
                    <p>Floor</p>
                    <p>{{ res.floor }}</p>
                </div>
                <div class="info">
                    <p>Floor side</p>
                    <p>{{ res.floorSide }}</p>
                </div>
                <div class="info">
                    <p>Room</p>
                    <p>{{ res.room }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    res: Object,
    index: Number
})
</script>