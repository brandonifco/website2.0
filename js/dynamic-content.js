// dynamic-content.js

// Load the content JSON dynamically
async function fetchContent() {
    try {
        const response = await fetch('content.json');
        return await response.json();
    } catch (error) {
        console.error('Error fetching content:', error);
        return {};
    }
}

// Populate content into the page dynamically
function populateContent(content) {
    // Find all elements with the "data-content" attribute
    const contentElements = document.querySelectorAll('[data-content]');

    contentElements.forEach((element) => {
        // Extract the key from the data-content attribute
        const key = element.getAttribute('data-content');

        // Use the key to retrieve the corresponding content from the JSON
        let value = content;
        const arrayKeyMatch = key.match(/(.+\[(\d+)\])/); // Check for array indices

        if (arrayKeyMatch) {
            // Handle array key with index (e.g., "whyInvisibleFence.technologyInnovations[0]")
            const baseKey = arrayKeyMatch[1];
            const index = parseInt(arrayKeyMatch[2], 10);
            const keys = baseKey.split('.');

            // Resolve base key to the array
            for (const k of keys) {
                if (value[k] !== undefined) {
                    value = value[k];
                } else {
                    value = null;
                    break;
                }
            }

            // Get the specific array item
            if (Array.isArray(value) && value[index] !== undefined) {
                value = value[index];
            } else {
                value = null;
            }
        } else {
            // Standard key resolution (non-array keys)
            const keys = key.split('.');
            for (const k of keys) {
                if (value[k] !== undefined) {
                    value = value[k];
                } else {
                    value = null;
                    break;
                }
            }
        }

        // Populate the content into the element if it exists
        if (value !== null) {
            if (element.tagName === 'IMG' && key.includes('ImageURL')) {
                // Special handling for image elements
                element.src = value;
            } else if (element.tagName === 'A' && key.includes('url')) {
                // Special handling for anchor elements
                element.href = value;
            } else if (element.tagName === 'IFRAME' && key.includes('embedURL')) {
                // Special handling for iframe elements
                element.src = value;
            } else if (Array.isArray(value)) {
                element.innerHTML = value.map((item) => {
                    if (item.title && item.description && item.url) {
                        // Handle service overview cards
                        return `
                            <div class="service-card">
                                <h2>${item.title}</h2>
                                <p>${item.description}</p>
                                <a href="${item.url}">LEARN MORE</a>
                            </div>`;
                    } else if (item.title && item.description) {
                        // Handle items with title and description (e.g., issuesAddressed)
                        return `
                            <div class="issue-card">
                                <h2>${item.title}</h2>
                                <p>${item.description}</p>
                            </div>`;
                    } else if (item.label && item.url) {
                        // Handle menu links and sitemap links
                        return `<li><a href="${item.url}">${item.label}</a></li>`;
                    } else if (item.text && item.author) {
                        // Handle testimonials
                        return `
                            <div class="testimonial">
                                <p>"${item.text}"</p>
                                <p><strong>- ${item.author}</strong></p>
                            </div>`;
                    } else if (item.name && item.logoURL) {
                        // Handle trust indicators
                        return `
                            <div class="trust-indicator">
                                <img src="${item.logoURL}" alt="${item.name}">
                                <p>${item.name}</p>
                            </div>`;
                    } else if (item.platform && item.url && item.iconURL) {
                        // Handle social media links
                        return `
                            <a href="${item.url}" class="social-media-link">
                                <img src="${item.iconURL}" alt="${item.platform}">
                            </a>`;
                    } else if (item.day && item.time) {
                        // Handle operating hours
                        return `<li>${item.day}: ${item.time}</li>`;
                    } else if (item.category && item.questions) {
                        // Handle FAQ categories
                        return `
                            <div class="faq-category">
                                <h2>${item.category}</h2>
                                <ul>
                                    ${item.questions
                                        .map(
                                            (question) => `
                                                <li>
                                                    <h3>${question.question}</h3>
                                                    <p>${question.answer}</p>
                                                </li>`
                                        )
                                        .join('')}
                                </ul>
                            </div>`;
                    } else if (item.question && item.answer) {
                        // Handle individual FAQ questions (fallback)
                        return `
                            <div class="faq-item">
                                <h3>${item.question}</h3>
                                <p>${item.answer}</p>
                            </div>`;
                    } else if (typeof item === 'string') {
                        // Handle simple string arrays
                        return `<li>${item}</li>`;
                    } else {
                        return `<div>${JSON.stringify(item)}</div>`; // Fallback for other cases
                    }
                }).join('');
            } else {
                element.textContent = value;
            }
        } else {
            console.warn(`No content found for key: ${key}`);
        }
    });
}

// Initialize the dynamic content population
document.addEventListener('DOMContentLoaded', async () => {
    const content = await fetchContent();
    populateContent(content);
});
