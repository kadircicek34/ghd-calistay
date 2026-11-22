/**
 * GH Çalıştay — membership / access end-to-end checks.
 *
 * Run against a disposable staging container:
 *
 *   BASE_URL=http://127.0.0.1:8180 \
 *   GHD_ADMIN_USER=kadir \
 *   GHD_ADMIN_PASSWORD='...' \
 *   NODE_PATH=/path/to/node_modules node tests/e2e/membership.spec.js
 *
 * Optional: CHROME_PATH (custom Chromium binary), SHOT_DIR (screenshot output).
 */
const { chromium } = require("playwright");
const fs = require("fs");
const path = require("path");

const BASE = process.env.BASE_URL || "http://127.0.0.1:8180";
const ADMIN_USER = process.env.GHD_ADMIN_USER || "kadir";
const ADMIN_PASSWORD = process.env.GHD_ADMIN_PASSWORD;
const SHOTS = process.env.SHOT_DIR || path.join(process.cwd(), "artifacts", "screenshots");
const FIXTURE = path.join(__dirname, "fixtures", "e-devlet.pdf");
const CHROME = process.env.CHROME_PATH || "";

if (!ADMIN_PASSWORD) {
    throw new Error("GHD_ADMIN_PASSWORD is required");
}

fs.mkdirSync(SHOTS, { recursive: true });

const suffix = Date.now().toString().slice(-8);
const applicant = {
    fullName: "E2E Test Üyesi",
    username: `e2e${suffix}`,
    email: `e2e${suffix}@example.com`,
    phone: "05551112233",
    birthDate: "1995-05-10",
    school: "Test Hukuk Fakültesi",
    graduationYear: "2020",
    password: "E2eTest123!",
};

const rejectedApplicant = {
    fullName: "E2E Red Test",
    username: `red${suffix}`,
    email: `red${suffix}@example.com`,
    phone: "05554445566",
    birthDate: "1992-02-20",
    school: "Red Test Hukuk Fakültesi",
    graduationYear: "2016",
    password: "E2eRed123!",
};

function assert(condition, message) {
    if (!condition) {
        throw new Error(message);
    }
}

async function screenshot(page, name, fullPage = true) {
    await page.screenshot({ path: path.join(SHOTS, name), fullPage });
}

async function login(page, username, password) {
    await page.goto(`${BASE}/login`, { waitUntil: "networkidle" });
    await page.fill('input[name="username"]', username);
    await page.fill('input[name="password"]', password);
    await Promise.all([
        page.waitForURL((url) => !url.pathname.endsWith("/login"), { timeout: 20000 }),
        page.click('button[type="submit"]'),
    ]);
}

async function fillApplication(page, data, legalStatus, documentType) {
    await page.goto(`${BASE}/basvuru`, { waitUntil: "networkidle" });
    await page.fill("#full_name", data.fullName);
    await page.fill("#username", data.username);
    await page.fill("#email", data.email);
    await page.fill("#phone", data.phone);
    await page.fill("#birth_date", data.birthDate);
    await page.check(`input[name="legal_status"][value="${legalStatus}"]`);
    await page.fill("#school", data.school);
    if (data.graduationYear) {
        await page.fill("#graduation_year", data.graduationYear);
    }
    await page.selectOption("#document_type", documentType);
    await page.setInputFiles("#document", FIXTURE);
    await page.fill("#password", data.password);
    await page.fill("#password_confirmation", data.password);
    await page.check('input[name="kvkk_consent"]');
    await page.check('input[name="terms_consent"]');
}

(async () => {
    const browser = await chromium.launch({
        headless: true,
        ...(CHROME ? { executablePath: CHROME } : {}),
        args: ["--no-sandbox"],
    });

    const publicContext = await browser.newContext({
        ignoreHTTPSErrors: true,
        locale: "tr-TR",
        viewport: { width: 1440, height: 1000 },
        extraHTTPHeaders: { "X-Forwarded-For": "203.0.113.10" },
    });
    const publicPage = await publicContext.newPage();

    // 1. Landing page
    const landingResponse = await publicPage.goto(`${BASE}/`, { waitUntil: "networkidle" });
    assert(landingResponse && landingResponse.status() === 200, "Landing page should return 200");
    assert((await publicPage.title()).includes("GH Çalıştay"), "Landing title should include GH Çalıştay");
    assert(await publicPage.locator(".ghd-hero").isVisible(), "Hero should be visible");
    assert(await publicPage.locator('a[href$="/basvuru"]').first().isVisible(), "Application CTA should be visible");
    await screenshot(publicPage, "01-landing-desktop.png");

    // 2. Mobile landing
    await publicPage.setViewportSize({ width: 390, height: 844 });
    await publicPage.goto(`${BASE}/`, { waitUntil: "networkidle" });
    const overflow = await publicPage.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
    assert(overflow <= 2, `Landing should not overflow horizontally (overflow=${overflow})`);
    await screenshot(publicPage, "02-landing-mobile.png");
    await publicPage.setViewportSize({ width: 1440, height: 1000 });

    // 3. Empty application must not be accepted.
    await publicPage.goto(`${BASE}/basvuru`, { waitUntil: "networkidle" });
    await publicPage.click('button[type="submit"]');
    await publicPage.waitForLoadState("networkidle");
    assert(await publicPage.locator(".ghd-alert-error").isVisible(), "Empty application should show an error summary");
    assert((await publicPage.locator(".ghd-alert-error").innerText()).includes("Form gönderilemedi"), "Error summary should be Turkish");
    const emptyErrors = await publicPage.locator(".ghd-field-error").allInnerTexts();
    assert(emptyErrors.some((text) => text.includes("KVKK")), "KVKK consent must be required");
    await screenshot(publicPage, "03-application-validation.png");

    // 3b. A non-document upload must be rejected by the server.
    await fillApplication(publicPage, {
        ...applicant,
        username: `${applicant.username}bad`,
        email: `bad${applicant.email}`,
    }, "student", "student_certificate");
    await publicPage.setInputFiles("#document", path.join(__dirname, "fixtures", "not-a-document.txt"));
    await publicPage.click('button[type="submit"]');
    await publicPage.waitForLoadState("networkidle");
    const uploadErrors = await publicPage.locator(".ghd-field-error").allInnerTexts();
    assert(uploadErrors.some((text) => text.includes("PDF, JPEG veya PNG")), "Invalid document type must be rejected");
    await screenshot(publicPage, "03b-application-invalid-file.png");

    // 4. A valid application is auto-approved.
    await fillApplication(publicPage, applicant, "student", "student_certificate");
    await Promise.all([
        publicPage.waitForURL(`${BASE}/basvuru/basarili`, { timeout: 20000 }),
        publicPage.click('button[type="submit"]'),
    ]);
    const successText = await publicPage.locator("body").innerText();
    assert(successText.includes("Üyeliğiniz onaylandı") || successText.includes("onaylandı"), "Success page should confirm auto-approval");
    assert(successText.includes("Panoya giriş yap"), "Auto-approved success page should offer a login CTA");
    await screenshot(publicPage, "04-application-success.png");

    // 5. Status is approved immediately; no admin step is required.
    await publicPage.goto(`${BASE}/basvuru/durum?identifier=${encodeURIComponent(applicant.email)}`, { waitUntil: "networkidle" });
    const approvedStatus = await publicPage.locator(".ghd-status-result").innerText();
    assert(approvedStatus.includes("Onaylandı"), "Status should show approved immediately");
    assert(await publicPage.locator('a.ghd-button[href$="/login"]').count() > 0, "Approved status should offer a login link");
    await screenshot(publicPage, "05-status-approved.png");

    // 6. The applicant can log in and use the board without admin action.
    const memberContext = await browser.newContext({
        ignoreHTTPSErrors: true,
        locale: "tr-TR",
        viewport: { width: 1440, height: 1000 },
        extraHTTPHeaders: { "X-Forwarded-For": "203.0.113.12" },
    });
    const memberPage = await memberContext.newPage();
    await login(memberPage, applicant.username, applicant.password);
    assert(memberPage.url().includes("/dashboard"), "Auto-approved member login should reach the dashboard");
    assert((await memberPage.locator("body").innerText()).includes("GH Çalıştay"), "Dashboard should show the GH Çalıştay project");
    await screenshot(memberPage, "06-member-dashboard.png");

    const boardResponse = await memberPage.goto(`${BASE}/board/1`, { waitUntil: "networkidle" });
    assert(boardResponse && boardResponse.status() === 200, "Approved member should open the board");
    const boardText = await memberPage.locator("body").innerText();
    assert(boardText.includes("Yapılacak") && boardText.includes("Devam Ediyor"), "Board should show Turkish columns");
    assert(boardText.includes("GH Çalıştay panosuna hoş geldiniz"), "Board should show the welcome task");
    await screenshot(memberPage, "06b-member-board.png");

    const forbidden = await memberPage.goto(`${BASE}/admin/uyelik-basvurulari`, { waitUntil: "networkidle" });
    assert(forbidden && forbidden.status() === 403, `Member must not access admin applications (status=${forbidden && forbidden.status()})`);
    await screenshot(memberPage, "09-member-admin-forbidden.png");

    // 7. Admin sees the auto-approved application, inspects the document and
    //    keeps the option to revoke the membership.
    const adminContext = await browser.newContext({
        ignoreHTTPSErrors: true,
        locale: "tr-TR",
        viewport: { width: 1440, height: 1000 },
        extraHTTPHeaders: { "X-Forwarded-For": "203.0.113.11" },
    });
    const adminPage = await adminContext.newPage();
    await login(adminPage, ADMIN_USER, ADMIN_PASSWORD);
    assert(adminPage.url().includes("/dashboard"), "Admin login should reach the dashboard");
    assert(await adminPage.locator(".ghd-admin-banner").count() > 0, "Recent auto-approvals should be surfaced on the admin dashboard");
    assert(await adminPage.locator('a[href*="/admin/uyelik-basvurulari"]').count() > 0, "Admin dropdown should link to membership applications");

    await adminPage.goto(`${BASE}/admin/uyelik-basvurulari`, { waitUntil: "networkidle" });
    assert((await adminPage.locator("body").innerText()).includes("Üyelik Başvuruları"), "Admin list should be visible");
    const row = adminPage.locator("table tbody tr", { hasText: applicant.email }).first();
    assert(await row.count() === 1, "Auto-approved application should be listed");
    await row.locator("a.btn").click();
    await adminPage.waitForLoadState("networkidle");
    const detailText = await adminPage.locator("body").innerText();
    assert(detailText.includes(applicant.fullName), "Detail should show full name");
    assert(detailText.includes("10.05.1995"), "Detail should show birth date");
    assert(detailText.includes("Hukuk öğrencisi"), "Detail should show legal status");
    assert(detailText.includes(applicant.school), "Detail should show school");
    assert(detailText.includes("Otomatik onaylandı"), "Detail should label the automatic approval");
    assert(await adminPage.locator("button.btn-green").count() === 0, "Auto-approved detail should not show a manual approve button");
    assert(await adminPage.locator("button.btn-red").count() === 1, "Auto-approved detail should offer membership revocation");
    const applicationId = (adminPage.url().match(/uyelik-basvurulari\/(\d+)/) || [])[1];
    assert(applicationId, "Application id should be parsed from the detail URL");
    await screenshot(adminPage, "07-admin-application-detail.png");

    const downloadPromise = adminPage.waitForEvent("download", { timeout: 20000 });
    await adminPage.click('a[href*="/belge"]');
    const download = await downloadPromise;
    assert((await download.suggestedFilename()).length > 0, "Document download should start");

    // 8. Admin revokes an auto-approved membership; the account is disabled.
    await fillApplication(publicPage, rejectedApplicant, "graduate", "diploma");
    await Promise.all([
        publicPage.waitForURL(`${BASE}/basvuru/basarili`, { timeout: 20000 }),
        publicPage.click('button[type="submit"]'),
    ]);

    const revokedContext = await browser.newContext({
        ignoreHTTPSErrors: true,
        locale: "tr-TR",
        viewport: { width: 1440, height: 1000 },
        extraHTTPHeaders: { "X-Forwarded-For": "203.0.113.13" },
    });
    const revokedPage = await revokedContext.newPage();
    await login(revokedPage, rejectedApplicant.username, rejectedApplicant.password);
    assert(revokedPage.url().includes("/dashboard"), "Revocation target should first be able to log in");

    await adminPage.goto(`${BASE}/admin/uyelik-basvurulari`, { waitUntil: "networkidle" });
    const rejectRow = adminPage.locator("table tbody tr", { hasText: rejectedApplicant.email }).first();
    assert(await rejectRow.count() === 1, "Revocation target should be listed");
    await rejectRow.locator("a.btn").click();
    await adminPage.waitForLoadState("networkidle");
    await adminPage.fill("#review_note", "E2E testi: belge doğrulaması tamamlanmadı.");
    adminPage.once("dialog", (dialog) => dialog.accept());
    await Promise.all([
        adminPage.waitForURL(`${BASE}/admin/uyelik-basvurulari`, { timeout: 20000 }),
        adminPage.click('button.btn-red'),
    ]);
    assert((await adminPage.locator("body").innerText()).includes("Üyelik iptal edildi"), "Revocation flash should be shown");
    await screenshot(adminPage, "08-admin-revoked.png");

    await publicPage.goto(`${BASE}/basvuru/durum?identifier=${encodeURIComponent(rejectedApplicant.email)}`, { waitUntil: "networkidle" });
    const rejectedText = await publicPage.locator(".ghd-status-result").innerText();
    assert(rejectedText.includes("Reddedildi"), "Status should show rejected after revocation");
    assert(rejectedText.includes("belge doğrulaması"), "Status should show revocation reason");
    await screenshot(publicPage, "10-status-rejected.png");

    await revokedPage.goto(`${BASE}/dashboard`, { waitUntil: "networkidle" });
    assert(revokedPage.url().includes("/login"), `Revoked account should be redirected to login (url=${revokedPage.url()})`);
    await screenshot(revokedPage, "08b-revoked-dashboard.png");

    await publicPage.goto(`${BASE}/login`, { waitUntil: "networkidle" });
    await publicPage.fill('input[name="username"]', rejectedApplicant.username);
    await publicPage.fill('input[name="password"]', rejectedApplicant.password);
    await publicPage.click('button[type="submit"]');
    await publicPage.waitForFunction(() => {
        const el = document.getElementById("ghd-login-status");
        return el && !el.hidden && el.textContent.includes("reddedildi");
    }, { timeout: 15000 });
    await screenshot(publicPage, "11-login-rejected.png");

    // 10. CSRF / unauthenticated document and application access.
    const documentUrl = `${BASE}/admin/uyelik-basvurulari/${applicationId}/belge`;
    const anonymousDocument = await publicContext.request.get(documentUrl, { maxRedirects: 0 });
    assert([302, 403].includes(anonymousDocument.status()), `Anonymous document access should be blocked (status=${anonymousDocument.status()})`);
    const memberDocument = await memberContext.request.get(documentUrl, { maxRedirects: 0 });
    assert([302, 403].includes(memberDocument.status()), `Member document access should be blocked (status=${memberDocument.status()})`);
    const adminWithoutToken = await adminContext.request.get(documentUrl, { maxRedirects: 0 });
    assert(adminWithoutToken.status() === 403, `Admin document access without CSRF must be blocked (status=${adminWithoutToken.status()})`);

    const anonymousDashboard = await publicContext.request.get(`${BASE}/dashboard`, { maxRedirects: 0 });
    assert(anonymousDashboard.status() === 302, `Anonymous dashboard should redirect to login (status=${anonymousDashboard.status()})`);

    const noCsrfApplication = await publicContext.request.post(`${BASE}/basvuru/gonder`, {
        form: { email: "csrf@example.com" },
        maxRedirects: 0,
    });
    assert(noCsrfApplication.status() === 403, `Application POST without CSRF must be rejected (status=${noCsrfApplication.status()})`);

    // 11. Approved email cannot be used for a second application.
    await fillApplication(publicPage, {
        ...applicant,
        username: `${applicant.username}x`,
    }, "student", "student_certificate");
    await publicPage.click('button[type="submit"]');
    await publicPage.waitForLoadState("networkidle");
    const duplicateErrors = await publicPage.locator(".ghd-field-error").allInnerTexts();
    console.log("duplicate field errors:", duplicateErrors.join(" | "));
    assert(
        duplicateErrors.some((text) => text.includes("onaylanmış bir üyelik") || text.includes("kayıtlı bir hesap")),
        "Approved email should be rejected for a new application"
    );
    await screenshot(publicPage, "13-duplicate-application.png");

    await browser.close();
    console.log("PASS");
    console.log(JSON.stringify({
        applicant: applicant.email,
        rejected: rejectedApplicant.email,
        applicationId,
        screenshots: SHOTS,
    }, null, 2));
})().catch(async (error) => {
    console.error("FAIL");
    console.error(error.stack || error);
    process.exit(1);
});
