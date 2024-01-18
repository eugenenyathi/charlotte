<BaseButton v-if="!page" text="Next" class="signup-btn" @btnFn="firstPage()" />
<BaseButton v-else text="Submit" class="signup-btn" @btnFn="secondPage()" />
