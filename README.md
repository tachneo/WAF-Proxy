# WAF Management Tool – User Manual

## Overview

The WAF Management Tool provides a user-friendly interface to protect your web application from security threats such as SQL injection, Cross-Site Scripting (XSS), and more. This manual will guide you through managing and configuring the WAF rules, logs, and settings through the WAF GUI.

![image](https://github.com/user-attachments/assets/98119269-fb27-457a-931f-b2820bc90c85)


## Main Features
- **Dashboard**: View statistics of requests, blocked attacks, and the current WAF status.
- **Logs**: Review logs of malicious and safe requests.
- **Rules Management**: Add, edit, or delete WAF rules.
- **Settings**: Enable/disable the WAF, configure rate limits, manage notifications, and other settings.
- **Audit**: View reports and audits of activity.
- **Export Logs**: Download logs for offline analysis.

---

## Accessing the WAF Tool

### 1. **Login**:
   - Navigate to the WAF login page (`https://yourdomain.com/waf/login.php`).
   - Enter your admin credentials and click **Login**.

### 2. **Logout**:
   - To log out, click the **Logout** button in the top right of the page.
   - This will securely end your session.

---

## Dashboard

Once logged in, the dashboard provides an overview of the current status of the WAF and traffic on your site.

### Dashboard Features
- **Total Requests Inspected**: The total number of requests processed by the WAF.
- **Malicious Requests Blocked**: Displays how many requests have been blocked due to security violations.
- **Approved (Safe) Requests**: Shows the number of legitimate, non-malicious requests.
- **Current WAF Status**: Indicates if the WAF is currently enabled or disabled. You can toggle the WAF status from here.
- **Threat Level**: Displays the current threat level (Normal, Medium, High Alert) based on malicious activity:
  - **Green**: Normal (Low threat)
  - **Orange**: Medium (Moderate threat)
  - **Red**: High Alert (High threat)

#### To Toggle WAF Status:
1. Click the **Toggle WAF** button.
2. Confirm the action when prompted.
3. The WAF status will update immediately.

---

## Logs

The **Logs** section allows you to view details about incoming requests and which ones were blocked by the WAF.

### Steps to View Logs:
1. Go to the **Logs** section via the navigation menu.
2. View details such as:
   - **IP Address**: The origin of the request.
   - **Request Endpoint**: The URL or endpoint that was accessed.
   - **Method**: The HTTP method used (GET, POST, etc.).
   - **Payload**: The data that was submitted with the request.
   - **Attack Type**: Identified attack patterns (SQL injection, XSS, etc.).
   - **Blocked Status**: Whether the request was blocked or allowed.

#### Search Logs:
- Use the search bar to filter logs based on IP addresses, endpoint, or method.

#### Clear Logs:
- If logs become too large, you can manually clear logs by navigating to the **Clear Logs** button at the bottom of the logs page.

![image](https://github.com/user-attachments/assets/10865fa7-3519-4e8b-932c-bdf91a36c634)

---

## Rules Management

In the **Rules** section, you can manage the rules that determine what the WAF will block or allow.

### Steps to Add a New Rule:
1. Navigate to the **Rules** section via the navigation menu.
2. Click the **Add New Rule** button.
3. Fill out the form:
   - **Rule Name**: A descriptive name for the rule (e.g., SQL Injection Protection).
   - **Pattern**: The regular expression pattern used to detect the attack (e.g., `/select|insert|drop/i`).
   - **Action**: Choose `block` to prevent the request or `log` to only log it.
4. Click **Submit** to save the new rule.

### Steps to Edit or Delete a Rule:
- **Edit**: Next to each rule, click the **Edit** button to update the pattern or action.
- **Delete**: Click the **Delete** button to remove the rule.

### To Update Rules from JSON:
1. In the **Rules** section, there is an option to upload a `.json` file (like `waf-rules.json`) to update rules in bulk.
2. Upload the updated rules file, and the WAF will automatically process and apply the changes.


![image](https://github.com/user-attachments/assets/e46180a0-2692-41ac-b9b1-bb02fd3c4370)


---

## Settings

The **Settings** section allows you to configure the core behavior of the WAF.

### Key Settings:
- **WAF Status**: Enable or disable the WAF.
- **Rate Limit**: Set the number of requests allowed per minute from a single IP address. This helps protect against DoS (Denial of Service) attacks.
- **Email Notifications**: Enable email alerts for suspicious or malicious activity.
- **Log Level**: Choose between logging all activity (`all`), only malicious activity (`malicious`), or disabling logging (`none`).

### Steps to Update WAF Settings:
1. Go to the **Settings** section.
2. Update the necessary settings (WAF status, rate limit, email, etc.).
3. Click **Update Settings** to save the changes.

### Admin Email Configuration:
- Enter the **Admin Email** where alerts will be sent when attacks are detected.
- Ensure that the email is up-to-date for critical notifications.

![image](https://github.com/user-attachments/assets/96f25e43-2773-46e9-99a1-9a7a93c929c3)

![image](https://github.com/user-attachments/assets/5360c0d1-b253-4151-b34d-79073ff87185)

---

## Audit

The **Audit** section allows you to view security audit reports generated by the WAF.

### To View an Audit Report:
1. Navigate to **Audit** in the menu.
2. The audit report will display data such as:
   - **Requests Inspected**
   - **Malicious Requests**
   - **Approved Requests**
3. You can filter audit results by date or event type.

![image](https://github.com/user-attachments/assets/6b3e699f-e56a-411c-8e4b-64e452fc7eff)

---

## Export Logs

The **Export Logs** feature allows you to download the WAF logs for offline analysis.

### Steps to Export Logs:
1. Navigate to **Export Logs** in the menu.
2. Choose the file format you want to export (e.g., JSON or CSV).
3. Click the **Export** button, and the logs will be downloaded to your device.

![image](https://github.com/user-attachments/assets/3056f9d9-3e02-401b-a018-35a0fb7f5a58)


---

## General Tips
- **Review Logs Regularly**: Regularly review WAF logs for patterns in malicious activity and adjust your rules accordingly.
- **Update Rules**: Keep your WAF rules up to date by reviewing the latest threats and updating the `waf-rules.json` file.
- **Monitor Rate Limits**: Set reasonable rate limits to protect against automated attacks while ensuring legitimate users are not blocked.

---

By following this manual, you’ll be able to efficiently manage your WAF through the GUI, enhancing the security of your web application.
