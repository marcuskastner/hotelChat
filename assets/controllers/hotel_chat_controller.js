import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['form', 'messages', 'input', 'submit'];

    async submit(event) {
        event.preventDefault();
        event.stopPropagation();

        if (this.submitTarget.disabled) {
            return;
        }

        const formData = new FormData(this.formTarget);

        this.submitTarget.disabled = true;

        try {
            const response = await fetch(this.formTarget.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Request failed: ${response.status}`);
            }

            const html = await response.text();

            this.messagesTarget.insertAdjacentHTML('beforeend', html);

            this.inputTarget.value = '';
            this.inputTarget.focus();
        } catch (error) {
            console.error('Hotel search failed:', error);
        } finally {
            this.submitTarget.disabled = false;
        }
    }
}
