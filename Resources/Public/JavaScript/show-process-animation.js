class ShowProcessAnimation {
    constructor() {
        document.querySelectorAll('[data-shows-process-animation-after-click="true"]').forEach(function (activator) {
            activator.addEventListener('click', function (event, processAnimationElement) {
                document.getElementById('db-rector-processing').style.display = 'flex';
            }, false);
        });
    };
}

export default new ShowProcessAnimation();


