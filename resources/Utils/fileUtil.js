
export const mapExistingImages = (listePj) => {
    const imageExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "webp"];

    let processedListePj = listePj;

    // Si c'est une chaîne JSON, on tente de la parser
    if (typeof listePj === "string") {
        try {
            processedListePj = JSON.parse(listePj);
        } catch (e) {
            console.error("Failed to parse JSON:", e);
            return [];
        }
    }

    // Vérifie que c'est bien un tableau après parsing
    if (!Array.isArray(processedListePj)) {
        return [];
    }

    return processedListePj
        .map((item) => {
            // Supporte les clés: path, src, url, main
            const path =
                typeof item === "string"
                    ? item
                    : item.path || item.src || item.url || item.main;

            if (!path) return null;

            const extension = path.split(".").pop()?.toLowerCase() || "";
            const isImage = imageExtensions.includes(extension);

            return {
                path: `../${path}`,
                isExisting: true,
                type: isImage ? "image/jpeg" : extension,
                name: item.name || path.split("/").pop(),
                size: item.size || 0,
                ...(typeof item === "object" ? item : {}),
            };
        })
        .filter(Boolean);
};
