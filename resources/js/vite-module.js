import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

/**
 * Collect the Vite entry paths exported by every enabled module's own
 * "vite.config.js" (its `export const paths = [...]`) so the root build
 * compiles each module's assets alongside the main app bundle.
 *
 * @param {string[]} paths
 * @param {string} modulesPath
 * @returns {Promise<string[]>}
 */
async function collectModuleAssetsPaths(paths, modulesPath) {
    const modulesDir = path.join(__dirname, modulesPath);
    const statusesPath = path.join(__dirname, 'modules_statuses.json');

    let statuses = {};

    try {
        statuses = JSON.parse(await fs.readFile(statusesPath, 'utf-8'));
    } catch {
        // No statuses file yet; treat every module as enabled.
    }

    let moduleDirectories = [];

    try {
        moduleDirectories = await fs.readdir(modulesDir, { withFileTypes: true });
    } catch {
        return paths;
    }

    for (const entry of moduleDirectories) {
        if (!entry.isDirectory() || statuses[entry.name] === false) {
            continue;
        }

        const viteConfigPath = path.join(modulesDir, entry.name, 'vite.config.js');

        try {
            await fs.access(viteConfigPath);
        } catch {
            continue;
        }

        const moduleConfig = await import(pathToFileURL(viteConfigPath).href);

        if (Array.isArray(moduleConfig.paths)) {
            paths.push(...moduleConfig.paths);
        }
    }

    return paths;
}

export default collectModuleAssetsPaths;
