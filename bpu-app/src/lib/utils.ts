const HTML_ENTITIES: Record<string, string> = {
    '&amp;':  '&',
    '&lt;':   '<',
    '&gt;':   '>',
    '&quot;': '"',
    '&#039;': "'",
    '&apos;': "'",
    '&nbsp;': ' ',
};

export function decodeHtml(str: string): string {
    if (!str) return str;
    return str.replace(/&(?:amp|lt|gt|quot|#039|apos|nbsp);/g, m => HTML_ENTITIES[m] ?? m);
}
