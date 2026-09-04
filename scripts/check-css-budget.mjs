#!/usr/bin/env node
/**
 * MSR Products CSS budget gate (estate consolidation CONS-B3c).
 *
 * Usage: node scripts/check-css-budget.mjs
 * Env: MSR_PRODUCTS_CSS_BUDGET_BYTES (default from config/css-budget.json)
 */
import { readFileSync, statSync, existsSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const ROOT = resolve( __dirname, '../../../../../../..' );
const budgetConfigPath = resolve( ROOT, 'config/css-budget.json' );
const budgetConfig = JSON.parse( readFileSync( budgetConfigPath, 'utf8' ) );
const themeBudget = budgetConfig.themes?.msrproducts || {};
const cssPath = resolve( __dirname, '../dist/app.css' );
const maxBytes = Number(
	process.env.MSR_PRODUCTS_CSS_BUDGET_BYTES || themeBudget.maxBytes || 380000
);

let size;
try {
	size = statSync( cssPath ).size;
} catch {
	console.error( `check-css-budget: missing ${cssPath} — run npm run production first` );
	process.exit( 1 );
}

const kb = ( size / 1024 ).toFixed( 1 );
const maxKb = ( maxBytes / 1024 ).toFixed( 1 );

if ( size > maxBytes ) {
	console.error( `check-css-budget: FAIL dist/app.css ${kb} KiB exceeds budget ${maxKb} KiB` );
	process.exit( 1 );
}

const css = readFileSync( cssPath, 'utf8' );
const cdnPatterns = budgetConfig.cdnHostPatterns || [
	'fonts.googleapis.com',
	'cdn.jsdelivr.net',
	'kit.fontawesome.com',
];
const cdnRe = new RegExp( cdnPatterns.map( ( h ) => h.replace( /\./g, '\\.' ) ).join( '|' ) );
if ( cdnRe.test( css ) ) {
	console.error( 'check-css-budget: FAIL dist/app.css still references external CDN hosts' );
	process.exit( 1 );
}

console.log( `check-css-budget: PASS dist/app.css ${kb} KiB (budget ${maxKb} KiB)` );
process.exit( 0 );
